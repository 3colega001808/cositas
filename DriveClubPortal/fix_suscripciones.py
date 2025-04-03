from app import app, db, PlanSuscripcion, SuscripcionUsuario, Usuario

with app.app_context():
    # Verificar todas las suscripciones
    suscripciones = SuscripcionUsuario.query.all()
    print(f"Total de suscripciones: {len(suscripciones)}")
    
    # Para cada suscripción, cargar explícitamente el plan y asegurar que esté correctamente enlazado
    for sub in suscripciones:
        try:
            # Verificar usuario
            usuario = Usuario.query.get(sub.usuario_id)
            if not usuario:
                print(f"ERROR: Usuario {sub.usuario_id} no encontrado para suscripción {sub.id}")
                continue
            
            # Verificar plan
            plan = PlanSuscripcion.query.get(sub.plan_id)
            if not plan:
                print(f"ERROR: Plan {sub.plan_id} no encontrado para suscripción {sub.id}")
                continue
                
            # Asegurar que el plan esté correctamente enlazado
            sub.plan = plan
            
            print(f"✓ Suscripción {sub.id}: Usuario={sub.usuario_id} ({usuario.nombre}), Plan={sub.plan_id} ({plan.nombre}), Activo={sub.activo}")
                
        except Exception as e:
            print(f"Error al procesar suscripción {sub.id}: {str(e)}")
    
    # Guardar cambios
    db.session.commit()
    print("Cambios guardados en la base de datos.")