import os
import json
import qrcode
import traceback
from datetime import datetime, timedelta, date
from io import BytesIO
import base64
from werkzeug.security import generate_password_hash, check_password_hash
from flask import Flask, render_template, redirect, url_for, request, jsonify, send_from_directory, flash, session
from flask_login import LoginManager, login_user, logout_user, login_required, current_user
from flask_wtf import FlaskForm
from models import db, Usuario, Rol, PlanSuscripcion, Vehiculo, SuscripcionUsuario, Marca, TipoVehiculo, Reserva, EstadoReserva, ImagenVehiculo

app = Flask(__name__, 
            static_folder='assets',
            template_folder='templates')

# Configuración de la aplicación
app.config['SECRET_KEY'] = os.environ.get("SESSION_SECRET", "driveclub_secret_key")
app.config['SQLALCHEMY_DATABASE_URI'] = os.environ.get("DATABASE_URL")
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.config['SQLALCHEMY_ENGINE_OPTIONS'] = {
    "pool_recycle": 300,
    "pool_pre_ping": True,
}

# Inicializar extensiones
db.init_app(app)
login_manager = LoginManager(app)
login_manager.login_view = 'login'

# Inicializar el esquema de base de datos
with app.app_context():
    db.create_all()

@login_manager.user_loader
def load_user(user_id):
    return Usuario.query.get(int(user_id))

# Utilidades
def format_price(price):
    return f"€{price:.2f}".replace('.', ',')

def format_date(date):
    if isinstance(date, str):
        date = datetime.strptime(date, '%Y-%m-%d')
    return date.strftime('%d/%m/%Y')

def generate_qr_code(data):
    qr = qrcode.QRCode(
        version=1,
        error_correction=qrcode.constants.ERROR_CORRECT_L,
        box_size=8,
        border=2,
    )
    qr.add_data(data)
    qr.make(fit=True)
    img = qr.make_image(fill_color="black", back_color="white")
    
    buffered = BytesIO()
    img.save(buffered)
    img_str = base64.b64encode(buffered.getvalue()).decode()
    
    # Crear una URL para el QR en lugar de guardar toda la imagen en la base de datos
    qr_id = str(hash(data + str(datetime.now())))[:10]
    qr_filename = f"qr_{qr_id}.png"
    qr_path = os.path.join('assets', 'images', 'qr', qr_filename)
    
    # Crear directorio si no existe
    os.makedirs(os.path.dirname(qr_path), exist_ok=True)
    
    # Guardar el archivo
    with open(qr_path, 'wb') as f:
        img.save(f)
    
    # Devolver la URL relativa
    return f"/assets/images/qr/{qr_filename}"

# Rutas para archivos estáticos de attached_assets
@app.route('/public/<path:filename>')
def serve_attached_assets(filename):
    return send_from_directory('attached_assets', filename)

@app.route('/src/<path:filename>')
def serve_src(filename):
    return send_from_directory('src', filename)

# Rutas para servir archivos CSS y JS
@app.route('/assets/css/<path:filename>')
def serve_css(filename):
    return send_from_directory('assets/css', filename)

@app.route('/assets/js/<path:filename>')
def serve_js(filename):
    return send_from_directory('assets/js', filename)

@app.route('/assets/images/<path:filename>')
def serve_images(filename):
    return send_from_directory('assets/images', filename)

# Funciones para datos
def get_featured_vehicles(limit=4):
    return Vehiculo.query.filter_by(activo=True, disponible=True).limit(limit).all()

def get_all_subscription_plans():
    return PlanSuscripcion.query.filter_by(activo=True).all()

def get_all_vehicle_types():
    return TipoVehiculo.query.all()

def get_all_brands():
    return Marca.query.all()
    
def get_filtered_vehicles(tipo_id=None, marca_id=None, plan_id=None):
    query = Vehiculo.query.filter_by(activo=True, disponible=True)
    
    if tipo_id and tipo_id != '0':
        query = query.filter_by(tipo_id=tipo_id)
    if marca_id and marca_id != '0':
        query = query.filter_by(marca_id=marca_id)
    if plan_id and plan_id != '0':
        query = query.filter_by(plan_minimo_id=plan_id)
        
    return query.all()

def get_vehicle_by_id(vehicle_id):
    return Vehiculo.query.get_or_404(vehicle_id)

# Rutas de la aplicación
@app.route('/')
def index():
    featured_vehicles = get_featured_vehicles(4)
    subscription_plans = get_all_subscription_plans()
    return render_template('index.html', 
                          featured_vehicles=featured_vehicles,
                          subscription_plans=subscription_plans,
                          format_price=format_price)

@app.route('/vehiculos')
def vehiculos():
    vehicle_types = get_all_vehicle_types()
    brands = get_all_brands()
    subscription_plans = get_all_subscription_plans()
    
    tipo_id = request.args.get('tipo_id', None)
    marca_id = request.args.get('marca_id', None)
    plan_id = request.args.get('plan_id', None)
    
    vehicles = get_filtered_vehicles(tipo_id, marca_id, plan_id)
    
    return render_template('vehiculos.html', 
                          vehicle_types=vehicle_types,
                          brands=brands,
                          subscription_plans=subscription_plans,
                          vehicles=vehicles,
                          format_price=format_price,
                          selected_tipo=tipo_id,
                          selected_marca=marca_id,
                          selected_plan=plan_id)

@app.route('/vehiculo/<int:vehicle_id>')
def vehiculo_detalle(vehicle_id):
    vehicle = get_vehicle_by_id(vehicle_id)
    
    # Para la fecha mínima en el formulario de reserva
    now_date = datetime.now().strftime('%Y-%m-%d')
    
    # Verificar si el usuario tiene una suscripción activa y si puede reservar este vehículo
    has_subscription = False
    can_reserve = False
    user_subscription = None
    
    if current_user.is_authenticated:
        user_subscription = SuscripcionUsuario.query.filter_by(
            usuario_id=current_user.id, 
            activo=True
        ).first()
        
        has_subscription = user_subscription is not None
        
        if has_subscription:
            can_reserve = user_subscription.plan.id >= vehicle.plan_minimo.id
    
    return render_template('vehiculo.html', 
                          vehicle=vehicle, 
                          format_price=format_price,
                          now_date=now_date,
                          has_subscription=has_subscription,
                          can_reserve=can_reserve,
                          user_subscription=user_subscription)

@app.route('/planes')
def planes():
    subscription_plans = get_all_subscription_plans()
    return render_template('planes.html', 
                          subscription_plans=subscription_plans,
                          format_price=format_price)

@app.route('/login', methods=['GET', 'POST'])
def login():
    if current_user.is_authenticated:
        return redirect(url_for('mi_cuenta'))
    
    registro = request.args.get('registro', 'false')
    
    if request.method == 'POST':
        if 'login' in request.form:
            email = request.form.get('email')
            password = request.form.get('password')
            
            user = Usuario.query.filter_by(email=email).first()
            if user and check_password_hash(user.password, password):
                login_user(user)
                user.ultimo_login = datetime.utcnow()
                db.session.commit()
                return redirect(url_for('mi_cuenta'))
            else:
                flash('Email o contraseña incorrectos', 'danger')
        
        elif 'register' in request.form:
            email = request.form.get('email')
            nombre = request.form.get('nombre')
            apellidos = request.form.get('apellidos')
            telefono = request.form.get('telefono')
            password = request.form.get('password')
            
            # Verificar si el email ya existe
            existing_user = Usuario.query.filter_by(email=email).first()
            if existing_user:
                flash('El email ya está registrado', 'danger')
                return redirect(url_for('login', registro='true'))
            
            # Crear nuevo usuario
            hashed_password = generate_password_hash(password)
            new_user = Usuario(
                email=email,
                nombre=nombre,
                apellidos=apellidos,
                telefono=telefono,
                password=hashed_password,
                rol_id=2  # Usuario regular
            )
            
            db.session.add(new_user)
            db.session.commit()
            
            # Iniciar sesión automáticamente
            login_user(new_user)
            return redirect(url_for('mi_cuenta'))
    
    return render_template('login.html', registro=(registro == 'true'))

@app.route('/logout')
@login_required
def logout():
    logout_user()
    return redirect(url_for('index'))

@app.route('/mi-cuenta', methods=['GET'])
@login_required
def mi_cuenta():
    # Obtener suscripciones activas del usuario actual
    suscripciones = []
    try:
        # Esto usa una consulta JOIN para obtener los planes asociados directamente
        suscripciones_query = db.session.query(SuscripcionUsuario).\
            options(db.joinedload(SuscripcionUsuario.plan)).\
            filter(SuscripcionUsuario.usuario_id == current_user.id)
            
        # Obtener todas las suscripciones
        todas_suscripciones = suscripciones_query.all()
        
        # Verificar si hay suscripciones y cargar explícitamente los planes si es necesario
        if todas_suscripciones:
            suscripciones = []
            for sub in todas_suscripciones:
                # Verificar si la relación plan está cargada
                if not hasattr(sub, 'plan') or not sub.plan:
                    # Cargar manualmente el plan
                    plan = PlanSuscripcion.query.get(sub.plan_id)
                    if plan:
                        sub.plan = plan
                
                # Solo añadir suscripciones con planes válidos
                if hasattr(sub, 'plan') and sub.plan:
                    suscripciones.append(sub)
                    print(f"Suscripción añadida: {sub.id}, Plan: {sub.plan.id} - {sub.plan.nombre}, Activo: {sub.activo}")
        
        # Verificación adicional para debugging
        print(f"Total suscripciones encontradas para usuario {current_user.id}: {len(suscripciones)}")
        for sub in suscripciones:
            print(f"Suscripción {sub.id}: Plan {sub.plan.id} - {sub.plan.nombre}, Activo: {sub.activo}")
    except Exception as e:
        print(f"Error crítico al cargar suscripciones: {str(e)}")
        print(f"Traceback: {traceback.format_exc()}")
        suscripciones = []
        
    # Obtener reservas del usuario con sus relaciones
    try:
        # Usar JOIN para cargar todas las relaciones de una vez
        reservas = db.session.query(Reserva).\
            options(
                db.joinedload(Reserva.vehiculo).joinedload(Vehiculo.tipo),
                db.joinedload(Reserva.vehiculo).joinedload(Vehiculo.marca),
                db.joinedload(Reserva.estado)
            ).\
            filter(Reserva.usuario_id == current_user.id).\
            order_by(Reserva.fecha_inicio.desc()).\
            all()
            
        # Verificación adicional de los datos cargados
        print(f"Reservas encontradas para usuario {current_user.id}: {len(reservas)}")
    except Exception as e:
        print(f"Error crítico al cargar reservas: {str(e)}")
        reservas = []
    
    # Fecha actual para comparar reservas
    now_date = date.today()
    
    # Preparar la información de suscripción activa
    active_subscription = None
    for sub in suscripciones:
        if sub.activo and hasattr(sub, 'plan') and sub.plan:
            active_subscription = sub
            print(f"ACTIVA: Suscripción {sub.id}, Plan {sub.plan.id} - {sub.plan.nombre}, Activo: {sub.activo}")
            break
    
    print(f"Active subscription seleccionada: {active_subscription}")
    
    # Crear un formulario con CSRF token para la seguridad
    form = FlaskForm()
    
    # Renderizar la plantilla con los datos cargados
    return render_template('mi-cuenta.html', 
                          suscripciones=suscripciones,
                          active_subscription=active_subscription,
                          reservas=reservas,
                          now_date=now_date,
                          format_price=format_price,
                          format_date=format_date,
                          form=form)

@app.route('/actualizar-perfil', methods=['POST'])
@login_required
def actualizar_perfil():
    form = FlaskForm()
    
    if form.validate_on_submit():
        try:
            # Actualizar datos básicos del usuario
            current_user.nombre = request.form.get('nombre')
            current_user.apellidos = request.form.get('apellidos')
            current_user.telefono = request.form.get('telefono')
            
            # Actualizar información personal
            fecha_nacimiento = request.form.get('fecha_nacimiento')
            if fecha_nacimiento:
                try:
                    current_user.fecha_nacimiento = datetime.strptime(fecha_nacimiento, '%Y-%m-%d').date()
                except:
                    pass
                    
            current_user.dni = request.form.get('dni')
            current_user.carnet_conducir = request.form.get('carnet_conducir')
            
            fecha_caducidad_carnet = request.form.get('fecha_caducidad_carnet')
            if fecha_caducidad_carnet:
                try:
                    current_user.fecha_caducidad_carnet = datetime.strptime(fecha_caducidad_carnet, '%Y-%m-%d').date()
                except:
                    pass
            
            # Actualizar dirección
            current_user.direccion = request.form.get('direccion')
            current_user.ciudad = request.form.get('ciudad')
            current_user.codigo_postal = request.form.get('codigo_postal')
            current_user.pais = request.form.get('pais')
            
            # Verificar si hay cambio de contraseña
            password_actual = request.form.get('password_actual')
            nuevo_password = request.form.get('nuevo_password')
            confirmar_password = request.form.get('confirmar_password')
            
            if password_actual and nuevo_password and confirmar_password:
                if not check_password_hash(current_user.password, password_actual):
                    flash('La contraseña actual es incorrecta', 'danger')
                    return redirect(url_for('mi_cuenta'))
                    
                if nuevo_password != confirmar_password:
                    flash('Las nuevas contraseñas no coinciden', 'danger')
                    return redirect(url_for('mi_cuenta'))
                    
                if len(nuevo_password) < 6:
                    flash('La nueva contraseña debe tener al menos 6 caracteres', 'danger')
                    return redirect(url_for('mi_cuenta'))
                    
                # Actualizar contraseña
                current_user.password = generate_password_hash(nuevo_password)
                flash('Contraseña actualizada correctamente', 'success')
            
            # Guardar cambios
            db.session.commit()
            flash('Perfil actualizado correctamente', 'success')
        except Exception as e:
            db.session.rollback()
            print(f"Error al actualizar perfil: {str(e)}")
            flash('Error al actualizar el perfil. Por favor, inténtalo de nuevo.', 'danger')
    else:
        flash('Error al actualizar el perfil. Por favor, inténtalo de nuevo.', 'danger')
    
    return redirect(url_for('mi_cuenta'))

@app.route('/reserva/<int:vehicle_id>', methods=['GET', 'POST'])
@login_required
def reserva(vehicle_id):
    vehicle = get_vehicle_by_id(vehicle_id)
    if not vehicle:
        flash('Vehículo no encontrado', 'danger')
        return redirect(url_for('vehiculos'))
    
    # Verificar que el usuario tiene una suscripción activa con JOIN explícito para el plan
    try:
        suscripcion = db.session.query(SuscripcionUsuario).\
            options(db.joinedload(SuscripcionUsuario.plan)).\
            filter(
                SuscripcionUsuario.usuario_id == current_user.id,
                SuscripcionUsuario.activo == True
            ).first()
        
        if not suscripcion:
            flash('Necesitas una suscripción activa para reservar. Por favor, suscríbete a un plan primero.', 'warning')
            return redirect(url_for('planes'))
        
        # Cargar plan explícitamente si no está cargado
        if not hasattr(suscripcion, 'plan') or not suscripcion.plan:
            plan = PlanSuscripcion.query.get(suscripcion.plan_id)
            if not plan:
                flash('Error al cargar el plan de suscripción. Por favor, contacta con atención al cliente.', 'danger')
                return redirect(url_for('planes'))
            suscripcion.plan = plan
        
        # Cargar plan mínimo del vehículo explícitamente
        if not hasattr(vehicle, 'plan_minimo') or not vehicle.plan_minimo:
            plan_minimo = PlanSuscripcion.query.get(vehicle.plan_minimo_id)
            if not plan_minimo:
                flash('Error al cargar información del vehículo. Por favor, inténtalo de nuevo.', 'danger')
                return redirect(url_for('vehiculos'))
            vehicle.plan_minimo = plan_minimo
            
        # Verificar que la suscripción cubre el plan mínimo del vehículo
        if suscripcion.plan.id < vehicle.plan_minimo.id:
            flash(f'Este vehículo requiere al menos el plan {vehicle.plan_minimo.nombre}. Por favor, actualiza tu plan.', 'warning')
            return redirect(url_for('planes'))
            
    except Exception as e:
        print(f"Error al verificar suscripción: {str(e)}")
        flash('Ha ocurrido un error al verificar tu suscripción. Por favor, inténtalo de nuevo.', 'danger')
        return redirect(url_for('planes'))
    
    if request.method == 'POST':
        try:
            fecha_inicio_str = request.form.get('fecha_inicio')
            fecha_fin_str = request.form.get('fecha_fin')
            ubicacion_recogida = request.form.get('ubicacion_recogida')
            
            # Validar fechas y convertirlas a objetos date
            from datetime import datetime
            try:
                fecha_inicio = datetime.strptime(fecha_inicio_str, '%Y-%m-%d').date()
                fecha_fin = datetime.strptime(fecha_fin_str, '%Y-%m-%d').date()
            except ValueError:
                flash('Formato de fecha inválido. Usa el formato YYYY-MM-DD.', 'danger')
                return render_template('reserva.html', vehicle=vehicle, format_price=format_price)
            
            # Validar que fecha_fin es posterior a fecha_inicio
            if fecha_fin <= fecha_inicio:
                flash('La fecha de finalización debe ser posterior a la fecha de inicio.', 'danger')
                return render_template('reserva.html', vehicle=vehicle, format_price=format_price)
            
            # Validar que no hay una reserva existente para este vehículo en ese periodo
            reserva_existente = Reserva.query.filter(
                Reserva.vehiculo_id == vehicle.id,
                Reserva.estado_id != 3,  # No cancelada
                db.or_(
                    db.and_(Reserva.fecha_inicio <= fecha_inicio, Reserva.fecha_fin >= fecha_inicio),
                    db.and_(Reserva.fecha_inicio <= fecha_fin, Reserva.fecha_fin >= fecha_fin),
                    db.and_(Reserva.fecha_inicio >= fecha_inicio, Reserva.fecha_fin <= fecha_fin)
                )
            ).first()
            
            if reserva_existente:
                flash('Este vehículo ya está reservado para las fechas seleccionadas.', 'danger')
                return render_template('reserva.html', vehicle=vehicle, format_price=format_price)
                
            # Crear reserva
            nueva_reserva = Reserva(
                usuario_id=current_user.id,
                vehiculo_id=vehicle.id,
                fecha_inicio=fecha_inicio,
                fecha_fin=fecha_fin,
                estado_id=1,  # Pendiente
                ubicacion_recogida=ubicacion_recogida,
                ubicacion_devolucion=ubicacion_recogida
            )
            
            db.session.add(nueva_reserva)
            db.session.commit()
            
            # Generar código QR
            qr_data = f"RESERVA:{nueva_reserva.id}|VEHICULO:{vehicle.id}|USUARIO:{current_user.id}|FECHA:{fecha_inicio}"
            qr_code = generate_qr_code(qr_data)
            nueva_reserva.codigo_qr = qr_code
            db.session.commit()
            
            # Actualizar disponibilidad del vehículo solo si la reserva empieza hoy
            if fecha_inicio == date.today():
                vehicle.disponible = False
                db.session.commit()
            
            flash('Reserva creada con éxito. Puedes verla en la sección de reservas.', 'success')
            return redirect(url_for('mi_cuenta'))
            
        except Exception as e:
            db.session.rollback()
            print(f"Error al crear reserva: {str(e)}")
            flash('Ocurrió un error al procesar tu reserva. Por favor, inténtalo de nuevo.', 'danger')
    
    # Crear formulario para la vista GET
    return render_template('reserva.html', 
                          vehicle=vehicle,
                          format_price=format_price)

# API routes
@app.route('/api/vehiculos')
def api_vehiculos():
    tipo_id = request.args.get('tipo_id', None)
    marca_id = request.args.get('marca_id', None)
    plan_id = request.args.get('plan_id', None)
    
    vehicles = get_filtered_vehicles(tipo_id, marca_id, plan_id)
    return jsonify([v.to_dict() for v in vehicles])

# Función para rastrear sesiones de usuario
@app.before_request
def track_user_session():
    session.permanent = True
    app.permanent_session_lifetime = timedelta(days=7)
    session.modified = True
    
    # Rastrear páginas visitadas
    if not session.get('visited_pages'):
        session['visited_pages'] = []
    
    if request.endpoint and request.endpoint != 'static':
        current_page = request.path
        visited_pages = session.get('visited_pages')
        if current_page not in visited_pages:
            visited_pages.append(current_page)
            session['visited_pages'] = visited_pages[:10]  # Mantener sólo las últimas 10

# Filtros de plantilla
@app.template_filter('format_price')
def filter_format_price(price):
    return format_price(price)

@app.template_filter('format_date')
def filter_format_date(date):
    return format_date(date)

# Contexto global para todas las plantillas
@app.context_processor
def inject_user():
    return dict(user=current_user)

@app.route('/suscribir-plan/<int:plan_id>', methods=['POST'])
@login_required
def suscribir_plan(plan_id):
    try:
        print(f"Intentando suscribir al usuario {current_user.id} al plan {plan_id}")
        
        # Obtener el plan seleccionado
        plan = db.session.get(PlanSuscripcion, plan_id)
        if not plan:
            print(f"Plan {plan_id} no encontrado")
            flash('El plan seleccionado no existe.', 'danger')
            return redirect(url_for('planes'))
            
        print(f"Plan encontrado: {plan.id} - {plan.nombre}")
        
        # Verificar si el usuario ya tiene una suscripción activa
        suscripcion_activa = db.session.query(SuscripcionUsuario).filter(
            SuscripcionUsuario.usuario_id == current_user.id,
            SuscripcionUsuario.activo == True
        ).first()
        
        if suscripcion_activa:
            print(f"Actualizando suscripción existente {suscripcion_activa.id} del plan {suscripcion_activa.plan_id} al plan {plan_id}")
            # Actualizar la suscripción existente
            suscripcion_activa.plan_id = plan_id
            suscripcion_activa.fecha_actualizacion = datetime.utcnow()
            flash(f'Tu suscripción ha sido actualizada al plan {plan.nombre}', 'success')
        else:
            print(f"Creando nueva suscripción para el usuario {current_user.id} al plan {plan_id}")
            # Crear nueva suscripción
            nueva_suscripcion = SuscripcionUsuario(
                usuario_id=current_user.id,
                plan_id=plan_id,
                fecha_inicio=date.today(),
                estado_pago='pendiente',
                activo=True
            )
            db.session.add(nueva_suscripcion)
            flash(f'¡Te has suscrito exitosamente al plan {plan.nombre}!', 'success')
        
        # Guardar cambios en la base de datos
        db.session.commit()
        
        # Imprimir para debug
        print(f"Suscripción procesada con éxito para el usuario {current_user.id}, plan {plan_id}")
        
        # Forzar una recarga de la sesión de usuario para actualizar datos
        db.session.refresh(current_user)
        
        # Redireccionar a la página de cuenta del usuario
        return redirect(url_for('mi_cuenta'))
        
    except Exception as e:
        db.session.rollback()
        print(f"Error al procesar suscripción: {str(e)}")
        print(f"Traceback: {traceback.format_exc()}")
        flash('Ha ocurrido un error al procesar tu suscripción. Por favor, inténtalo de nuevo.', 'danger')
        return redirect(url_for('planes'))

@app.route('/agregar-metodo-pago', methods=['POST'])
@login_required
def agregar_metodo_pago():
    form = FlaskForm()
    
    if form.validate_on_submit():
        try:
            # Obtener información de la tarjeta
            card_number = request.form.get('card_number')
            expiry_date = request.form.get('expiry_date')
            cardholder_name = request.form.get('cardholder_name')
            
            # Ocultar la mayoría de los dígitos para mayor seguridad
            last_digits = card_number.replace(' ', '')[-4:]
            masked_card = f"**** **** **** {last_digits}"
            
            # Crear cadena formateada para mostrar
            metodo_pago = f"{masked_card} | {cardholder_name} | Exp: {expiry_date}"
            
            # Guardar en la base de datos
            current_user.metodo_pago = metodo_pago
            db.session.commit()
            
            flash('Método de pago añadido correctamente', 'success')
        except Exception as e:
            db.session.rollback()
            print(f"Error al añadir método de pago: {str(e)}")
            flash('Error al añadir método de pago. Por favor, inténtalo de nuevo.', 'danger')
    else:
        flash('Formulario no válido. Por favor, inténtalo de nuevo.', 'danger')
    
    return redirect(url_for('mi_cuenta'))

@app.route('/eliminar-metodo-pago')
@login_required
def eliminar_metodo_pago():
    try:
        current_user.metodo_pago = None
        db.session.commit()
        flash('Método de pago eliminado correctamente', 'success')
    except Exception as e:
        db.session.rollback()
        print(f"Error al eliminar método de pago: {str(e)}")
        flash('Error al eliminar método de pago. Por favor, inténtalo de nuevo.', 'danger')
    
    return redirect(url_for('mi_cuenta'))

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)