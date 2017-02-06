-- ## IMPORTANTE ##
-- Este script resuelve los conflicots de usuarios repetidos con uno de los varios criterios que se pueden adoptar
-- Conserva la contraseña e id de la tabla "usuariomosp"
-- Ademas, los id de los usuarios de sgo (tabla "usuario") no repetidos será redefinido por uno nuevo
-- ################


-- Agregadas las columnas de permisos SGO y TABLERO
ALTER TABLE usuariomosp 
	ADD COLUMN Sgo TINYINT NOT NULL AFTER IdPlanilla,
	ADD COLUMN Tablero TINYINT NOT NULL AFTER Sgo;

-- Permisos de TABLERO agregados
UPDATE usuariomosp SET Tablero = 1 WHERE IdPlanilla <> '';

-- Permisos de SGO agregados a los repetidos
UPDATE usuariomosp AS u INNER JOIN usuario AS u2 ON u.Username = u2.Username SET usuariomosp.Sgo = 1;

-- Usuarios con permiso de SGO no repetidos agregados a la tabla unificada
INSERT INTO usuariomosp (
	SELECT 
		NULL AS IdUsuario, 
		u1.ApellidoNombre, 
		u1.Username, 
		u1.Password, 
		'' AS IdPlanilla, 
		1 AS Sgo, 
		0 AS Tablero 
	FROM 
		usuario AS u1
		LEFT JOIN usuariomosp AS u2 ON u1.Username = u2.Username
	WHERE
		u2.Username IS NULL
)

-- Datos de SGO trasladados a Nueva Tabla:
CREATE TABLE usuario_sgo (
	SELECT 
		u2.IdUsuario AS IdUsuario, 
		u1.IdOrganismo AS IdOrganismo, 
		u1.Activo AS Activo, 
		u1.IdRol AS IdRol, 
		IFNULL(u1.Email, '') AS Email
	FROM 
		usuario AS u1
		INNER JOIN usuariomosp AS u2 ON u1.Username = u2.Username
);
ALTER TABLE usuario_sgo 
	ADD INDEX fk_suario (IdUsuario ASC),
	ADD INDEX fk_usuario_organismo (IdOrganismo ASC),
	ADD INDEX fk_usuario_rol (IdRol ASC);

---------------------
-- FIN DE LOS CAMBIOS	
---------------------
	
	
-- CONSULTA TABLA SGO
SELECT * 
FROM 
	usuariomosp AS usuario 
	LEFT JOIN usuario_sgo AS usuario_sgo ON usuario.IdUsuario = usuario_sgo.IdUsuario 
WHERE usuario.Sgo = 1;

-- CONSULTA TABLA TABLERO
SELECT * FROM usuariomosp AS usuario WHERE usuario.Tablero = 1;



SELECT 
	usuario.IdUsuario,
	usuario.IdPlanilla,
	usuario.ApellidoNombre,
	usuario.Sgo,
	usuario.Tablero,
	usuario_sgo.Activo,
	usuario_sgo.IdOrganismo,
	usuario_sgo.IdRol,
	usuario_sgo.Email,
FROM 
	usuariomosp AS usuario 
	LEFT JOIN usuario_sgo AS usuario_sgo ON usuario.IdUsuario = usuario_sgo.IdUsuario;
