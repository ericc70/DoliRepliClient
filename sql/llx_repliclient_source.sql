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


CREATE TABLE llx_repliclient_source(
	-- BEGIN MODULEBUILDER FIELDS
	rowid int AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	name varchar(255) NOT NULL, 
	url varchar(255), 
	content text,
	keyauth varchar(255)
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
