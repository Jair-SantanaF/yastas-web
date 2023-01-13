DROP EVENT  tokens_recuperar_password;
CREATE EVENT tokens_recuperar_password
  ON SCHEDULE
    EVERY 1 DAY
      STARTS (TIMESTAMP(CURRENT_DATE) + INTERVAL 1 DAY + INTERVAL 1 HOUR)
  COMMENT 'Limpia los tokens no utilizados con más de un mes que no se ocuparon'
DO
  update recuperar_password set estatus = 0
WHERE TIMESTAMPDIFF(DAY, fecha, NOW()) >= 30 and estatus = 1;