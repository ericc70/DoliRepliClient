-- Copyright (C) 2024 SuperAdmin
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


-- BEGIN MODULEBUILDER INDEXES
-- END MODULEBUILDER INDEXES

--ALTER TABLE llx_repliclient_suivit ADD UNIQUE INDEX uk_repliclient_suivit_fieldxy(fieldx, fieldy);

--ALTER TABLE llx_repliclient_suivit ADD CONSTRAINT llx_repliclient_suivit_fk_field FOREIGN KEY (fk_field) REFERENCES llx_repliclient_myotherobject(rowid);

-- ALTER TABLE `llx_repliclient_suivit` ADD CONSTRAINT `suivit_demadeId` FOREIGN KEY (`fk_demande`) REFERENCES `llx_repliclient_demande`(`rowid`) ON DELETE CASCADE ON UPDATE NO ACTION; 

ALTER TABLE `llx_repliclient_suivit`
  ADD CONSTRAINT `llx_repliclient_suivit_ibfk_1` FOREIGN KEY (`fk_demande`) REFERENCES `llx_repliclient_demande` (`rowid`) ON DELETE CASCADE ON UPDATE RESTRICT;
