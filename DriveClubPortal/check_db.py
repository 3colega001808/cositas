from app import app, db, PlanSuscripcion, SuscripcionUsuario

with app.app_context():
    planes = PlanSuscripcion.query.all()
    print("Planes de suscripción:")
    for plan in planes:
        print(f"ID: {plan.id}, Nombre: {plan.nombre}, Precio: {plan.precio_mensual}")
    
    suscripciones = SuscripcionUsuario.query.all()
    print("\nSuscripciones:")
    for sub in suscripciones:
        print(f"ID: {sub.id}, Usuario: {sub.usuario_id}, Plan: {sub.plan_id}, Activo: {sub.activo}")