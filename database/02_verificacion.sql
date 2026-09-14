SELECT id,nombre,descripcion FROM roles ORDER BY id;
SELECT id,nombre FROM clientes ORDER BY id;
SELECT r.nombre rol, COUNT(rp.permiso_id) permisos FROM roles r LEFT JOIN rol_permisos rp ON rp.rol_id=r.id GROUP BY r.id,r.nombre ORDER BY r.id;
SELECT u.id,u.nombre_completo,u.email,u.rol_id,u.cliente_id,c.nombre cliente FROM usuarios u LEFT JOIN clientes c ON c.id=u.cliente_id ORDER BY u.id;
