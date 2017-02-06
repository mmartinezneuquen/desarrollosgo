-- Modificación para incorporar el ID de sesión al registro del Login
ALTER TABLE `ingreso` 
ADD COLUMN `SessionId` CHAR(26) NOT NULL AFTER `Ip`;