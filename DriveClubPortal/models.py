from flask_sqlalchemy import SQLAlchemy
from flask_login import UserMixin
from datetime import datetime
import json

db = SQLAlchemy()

class Rol(db.Model):
    __tablename__ = 'roles'
    id = db.Column(db.Integer, primary_key=True)
    nombre = db.Column(db.String(50), unique=True, nullable=False)
    descripcion = db.Column(db.Text)
    fecha_creacion = db.Column(db.DateTime, default=datetime.utcnow)
    fecha_actualizacion = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    usuarios = db.relationship('Usuario', backref='rol', lazy=True)

class Usuario(db.Model, UserMixin):
    __tablename__ = 'usuarios'
    id = db.Column(db.Integer, primary_key=True)
    email = db.Column(db.String(100), unique=True, nullable=False)
    password = db.Column(db.String(255), nullable=False)
    nombre = db.Column(db.String(100), nullable=False)
    apellidos = db.Column(db.String(100), nullable=False)
    telefono = db.Column(db.String(20))
    rol_id = db.Column(db.Integer, db.ForeignKey('roles.id'), nullable=False, default=2)
    activo = db.Column(db.Boolean, nullable=False, default=True)
    ultimo_login = db.Column(db.DateTime)
    fecha_creacion = db.Column(db.DateTime, default=datetime.utcnow)
    fecha_actualizacion = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Campos adicionales para el perfil
    fecha_nacimiento = db.Column(db.Date, nullable=True)
    dni = db.Column(db.String(15), nullable=True)
    carnet_conducir = db.Column(db.String(20), nullable=True)
    fecha_caducidad_carnet = db.Column(db.Date, nullable=True)
    direccion = db.Column(db.String(255), nullable=True)
    ciudad = db.Column(db.String(100), nullable=True)
    codigo_postal = db.Column(db.String(10), nullable=True)
    pais = db.Column(db.String(100), nullable=True)
    metodo_pago = db.Column(db.String(255), nullable=True)
    
    reservas = db.relationship('Reserva', backref='usuario', lazy=True)
    suscripciones = db.relationship('SuscripcionUsuario', backref='usuario', lazy=True)

class PlanSuscripcion(db.Model):
    __tablename__ = 'planes_suscripcion'
    id = db.Column(db.Integer, primary_key=True)
    nombre = db.Column(db.String(50), unique=True, nullable=False)
    precio_mensual = db.Column(db.Numeric(10, 2), nullable=False)
    descripcion = db.Column(db.Text)
    limite_vehiculos = db.Column(db.Integer)
    limite_duracion = db.Column(db.Integer)
    kilometraje_mensual = db.Column(db.Integer)
    caracteristicas_especiales = db.Column(db.Text)
    activo = db.Column(db.Boolean, nullable=False, default=True)
    
    vehiculos = db.relationship('Vehiculo', backref='plan_minimo', lazy=True)
    suscripciones = db.relationship('SuscripcionUsuario', backref='plan', lazy=True)
    
    def get_caracteristicas(self):
        if self.caracteristicas_especiales:
            try:
                return json.loads(self.caracteristicas_especiales)
            except:
                return []
        # Características predeterminadas basadas en el nivel del plan
        caracteristicas = []
        if self.id == 1:  # Plan Básico
            caracteristicas = [
                "Acceso a vehículos básicos",
                "Cambio de vehículo mensual",
                f"{self.kilometraje_mensual} km mensuales",
                "Mantenimiento básico incluido",
                "Seguro a terceros",
                "No Asistencia en carretera",
                "No Entrega y recogida",
                "No Acceso a eventos exclusivos"
            ]
        elif self.id == 2:  # Plan Premium
            caracteristicas = [
                "Acceso a vehículos premium",
                "Cambio de vehículo quincenal",
                f"{self.kilometraje_mensual} km mensuales",
                "Mantenimiento básico incluido",
                "Seguro a todo riesgo",
                "Asistencia en carretera",
                "No Entrega y recogida",
                "No Acceso a eventos exclusivos"
            ]
        elif self.id == 3:  # Plan Elite
            caracteristicas = [
                "Acceso a todos los vehículos",
                "Cambio de vehículo semanal",
                f"{self.kilometraje_mensual} km mensuales",
                "Mantenimiento completo incluido",
                "Seguro a todo riesgo",
                "Asistencia en carretera premium",
                "Entrega y recogida",
                "Acceso a eventos exclusivos"
            ]
        return caracteristicas

class SuscripcionUsuario(db.Model):
    __tablename__ = 'suscripciones_usuario'
    id = db.Column(db.Integer, primary_key=True)
    usuario_id = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)
    plan_id = db.Column(db.Integer, db.ForeignKey('planes_suscripcion.id'), nullable=False)
    fecha_inicio = db.Column(db.Date, nullable=False)
    fecha_fin = db.Column(db.Date)
    estado_pago = db.Column(db.String(20), nullable=False, default='pendiente')
    activo = db.Column(db.Boolean, nullable=False, default=True)
    fecha_creacion = db.Column(db.DateTime, default=datetime.utcnow)
    fecha_actualizacion = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

class Marca(db.Model):
    __tablename__ = 'marcas'
    id = db.Column(db.Integer, primary_key=True)
    nombre = db.Column(db.String(100), unique=True, nullable=False)
    logo_url = db.Column(db.String(255))
    
    vehiculos = db.relationship('Vehiculo', backref='marca', lazy=True)

class TipoVehiculo(db.Model):
    __tablename__ = 'tipos_vehiculo'
    id = db.Column(db.Integer, primary_key=True)
    nombre = db.Column(db.String(100), unique=True, nullable=False)
    
    vehiculos = db.relationship('Vehiculo', backref='tipo', lazy=True)

class Vehiculo(db.Model):
    __tablename__ = 'vehiculos'
    id = db.Column(db.Integer, primary_key=True)
    nombre = db.Column(db.String(100), nullable=False)
    marca_id = db.Column(db.Integer, db.ForeignKey('marcas.id'), nullable=False)
    tipo_id = db.Column(db.Integer, db.ForeignKey('tipos_vehiculo.id'), nullable=False)
    año = db.Column(db.Integer, nullable=False)
    matricula = db.Column(db.String(20), unique=True, nullable=False)
    potencia = db.Column(db.String(50))
    aceleracion = db.Column(db.String(50))
    traccion = db.Column(db.String(50))
    descripcion = db.Column(db.Text)
    plan_minimo_id = db.Column(db.Integer, db.ForeignKey('planes_suscripcion.id'), nullable=False)
    transmision = db.Column(db.String(50))
    consumo_combustible = db.Column(db.String(50))
    velocidad_maxima = db.Column(db.String(50))
    activo = db.Column(db.Boolean, nullable=False, default=True)
    disponible = db.Column(db.Boolean, nullable=False, default=True)
    ubicacion = db.Column(db.String(255))
    tarifa_diaria = db.Column(db.Numeric(10, 2))
    
    imagenes = db.relationship('ImagenVehiculo', backref='vehiculo', lazy=True)
    reservas = db.relationship('Reserva', backref='vehiculo', lazy=True)
    
    def to_dict(self):
        return {
            'id': self.id,
            'nombre': self.nombre,
            'marca_nombre': self.marca.nombre,
            'tipo_nombre': self.tipo.nombre,
            'año': self.año,
            'matricula': self.matricula,
            'potencia': self.potencia,
            'aceleracion': self.aceleracion,
            'traccion': self.traccion,
            'plan_nombre': self.plan_minimo.nombre,
            'precio_mensual': float(self.plan_minimo.precio_mensual),
            'imagen': self.get_imagen_principal()
        }
    
    def get_imagen_principal(self):
        imagen_principal = ImagenVehiculo.query.filter_by(vehiculo_id=self.id, es_principal=True).first()
        if imagen_principal:
            return imagen_principal.url_imagen
        # Devolver la primera imagen si no hay una principal
        imagen = ImagenVehiculo.query.filter_by(vehiculo_id=self.id).first()
        if imagen:
            return imagen.url_imagen
        return None

class ImagenVehiculo(db.Model):
    __tablename__ = 'imagenes_vehiculo'
    id = db.Column(db.Integer, primary_key=True)
    vehiculo_id = db.Column(db.Integer, db.ForeignKey('vehiculos.id'), nullable=False)
    url_imagen = db.Column(db.String(255), nullable=False)
    es_principal = db.Column(db.Boolean, nullable=False, default=False)
    orden_visualizacion = db.Column(db.Integer, nullable=False, default=0)

class EstadoReserva(db.Model):
    __tablename__ = 'estados_reserva'
    id = db.Column(db.Integer, primary_key=True)
    nombre = db.Column(db.String(50), unique=True, nullable=False)
    
    reservas = db.relationship('Reserva', backref='estado', lazy=True)

class Reserva(db.Model):
    __tablename__ = 'reservas'
    id = db.Column(db.Integer, primary_key=True)
    usuario_id = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)
    vehiculo_id = db.Column(db.Integer, db.ForeignKey('vehiculos.id'), nullable=False)
    fecha_inicio = db.Column(db.Date, nullable=False)
    fecha_fin = db.Column(db.Date, nullable=False)
    estado_id = db.Column(db.Integer, db.ForeignKey('estados_reserva.id'), nullable=False)
    codigo_qr = db.Column(db.String(255))
    ubicacion_recogida = db.Column(db.String(255))
    ubicacion_devolucion = db.Column(db.String(255))
    precio_total = db.Column(db.Numeric(10, 2))
    fecha_creacion = db.Column(db.DateTime, default=datetime.utcnow)