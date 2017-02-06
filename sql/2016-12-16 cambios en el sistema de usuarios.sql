-- Volvemos a unificar la tabla
ALTER TABLE `usuariomosp` 
ADD COLUMN `IdOrganismo` INT NOT NULL AFTER `Tablero`,
ADD COLUMN `Activo` TINYINT NOT NULL AFTER `IdOrganismo`,
ADD COLUMN `IdRol` INT NOT NULL AFTER `Activo`,
ADD COLUMN `Email` VARCHAR(100) NOT NULL AFTER `IdRol`;

-- Pasamos los valores
UPDATE usuariomosp AS u LEFT JOIN usuario_sgo AS u2 ON u.IdUsuario = u2.IdUsuario
SET u.IdOrganismo = u2.IdOrganismo, 
u.Activo = u2.Activo, 
u.IdRol = u2.IdRol, 
u.Email = u2.Email;





-- Creamos los botones...
CREATE TABLE `boton` (
  `IdBoton` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(45) NULL,
  PRIMARY KEY (`IdBoton`)
 );

INSERT INTO `boton` (`IdBoton`, `Nombre`) VALUES ('1', 'SGO');
INSERT INTO `boton` (`IdBoton`, `Nombre`) VALUES ('2', 'Tablero');
INSERT INTO `boton` (`IdBoton`, `Nombre`) VALUES ('3', 'Calendario');
INSERT INTO `boton` (`IdBoton`, `Nombre`) VALUES ('4', 'Tablero Unificado');
INSERT INTO `boton` (`IdBoton`, `Nombre`) VALUES ('5', 'Geo');
INSERT INTO `boton` (`IdBoton`, `Nombre`) VALUES ('6', 'Compromisos');


-- ...Y sus roles
CREATE TABLE `rolboton` (
  `IdRol` INT NOT NULL,
  `IdBoton` INT NOT NULL,
  PRIMARY KEY (`IdRol`, `IdBoton`)
);
 
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('1', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('2', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('3', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('4', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('5', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('6', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('7', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('8', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('9', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('10', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('11', '1');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('1', '2');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('2', '2');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('6', '3');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('9', '3');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('1', '4');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('2', '4');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('1', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('2', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('3', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('4', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('5', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('6', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('7', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('8', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('9', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('10', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('11', '5');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('8', '6');
INSERT INTO `rolboton` (`IdRol`, `IdBoton`) VALUES ('9', '6');

 
 
 
-- Cambiamos el nombre 'usuariomosp' a 'usuario'...
ALTER TABLE `usuario` 
RENAME TO  `usuario_viejo`;

ALTER TABLE `usuariomosp` 
RENAME TO  `usuario`;