# create the structure with doctrine before!

INSERT INTO `%db.name%`.`users` (`email`, `password`) VALUES ('p.scheit@ps-webforge.com', '583cdd008f2ea237bfe4d39a2d827f42');
INSERT INTO `%db.name%`.`users` (`email`, `password`) VALUES ('system@ps-webforge.com', '75a3ffa4254d64bfcbbf347cb851ecbc');

# this will not work permanently, when you have a fixture enabled
INSERT INTO `%db.name%_tests`.`users` (`email`, `password`) VALUES ('p.scheit@ps-webforge.com', '583cdd008f2ea237bfe4d39a2d827f42');
INSERT INTO `%db.name%_tests`.`users` (`email`, `password`) VALUES ('system@ps-webforge.com', '75a3ffa4254d64bfcbbf347cb851ecbc');
