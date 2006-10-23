<?php
 // $Id$
 //
 // Authors:
 //      Jeff Buchbinder <jeff@freemedsoftware.org>
 //
 // FreeMED Electronic Medical Record and Practice Management System
 // Copyright (C) 1999-2006 FreeMED Software Foundation
 //
 // This program is free software; you can redistribute it and/or modify
 // it under the terms of the GNU General Public License as published by
 // the Free Software Foundation; either version 2 of the License, or
 // (at your option) any later version.
 //
 // This program is distributed in the hope that it will be useful,
 // but WITHOUT ANY WARRANTY; without even the implied warranty of
 // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 // GNU General Public License for more details.
 //
 // You should have received a copy of the GNU General Public License
 // along with this program; if not, write to the Free Software
 // Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

include_once ( dirname(__FILE__).'/bootstrap.test.php' );

t("org.freemedsoftware.api.Messages.send", CallMethod('org.freemedsoftware.api.Messages.send', array('system' => 1, 'user' => 1, 'patient' => 1, 'subject' => 'Regression test', 'text' => 'This is a test of the regression testing mechanism for FreeMED\'s messaging system.')));
t("org.freemedsoftware.api.Messages.view", CallMethod('org.freemedsoftware.api.Messages.view'));

?>
