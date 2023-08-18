INSERT IGNORE INTO ps_carrier_zone (id_carrier, id_zone)
SELECT c.id_carrier, z.id_zone
FROM ps_carrier c
         JOIN ps_zone z;