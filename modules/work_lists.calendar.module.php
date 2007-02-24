<?php
 // $Id$
 //
 // Authors:
 // 	Jeff Buchbinder <jeff@freemedsoftware.org>
 //
 // FreeMED Electronic Medical Record and Practice Management System
 // Copyright (C) 1999-2007 FreeMED Software Foundation
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

LoadObjectDependency('org.freemedsoftware.core.BaseModule');

class WorkListsModule extends BaseModule {

	var $MODULE_NAME = "Work Lists";
	var $MODULE_VERSION = "0.1";
	var $MODULE_FILE = __FILE__;
	var $MODULE_UID = "f49d3432-1682-49c7-a3db-f5ff0d93c2b3";
	var $PACKAGE_MINIMUM_VERSION = "0.8.2";

	public function __construct ( ) {
		// __("Work Lists")

		// Add main menu notification handlers
		$this->_SetHandler( 'Dashboard', get_class($this) );
		$this->_SetHandler('MainMenu', 'notify');

		/*
		// Form proper configuration information
		$this->_SetMetaInformation('global_config_vars', array(
			'worklist_enabled',
			'worklist_providers'
		));
		$this->_SetMetaInformation('global_config', array(
			__("Show Work Lists") =>
			'html_form::select_widget ( '.
			'"worklist_enabled", '.
			'array ('.
				__("enabled").' => 1, '.
				__("disabled").' => 0, '.
			' ) ) ',
			__("Work List Providers") =>
			'freemed::multiple_choice ( '.
				'"SELECT phylname, phyfname, id '.
				'FROM physician WHERE phyref=\'no\' '.
				'ORDER BY phylname, phyfname", '.
				'"##phylname##, ##phyfname##", '.
				'"worklist_providers", fm_join_from_array($worklist_providers), '.
				'false )'
			)
		);
		*/
		
		// Call parent constructor
		parent::__construct ( );
	} // end constructor WorkListsModule

	function notify ( ) {
		global $display_buffer;

		$enable = freemed::config_value( 'worklist_enabled' );
		$providers = freemed::config_value( 'worklist_providers' );

		// Skip if this is not enabled
		if (!$enable) { return false; }

		include_once(freemed::template_file('ajax.php'));

		$display_buffer .= "
		<script language=\"javascript\">
		var workListElementActive = 0;
		var workListElementCurrent;

		function workListClick ( id ) {
			if (workListElementActive) { return false; }
			workListElementCurrent = id;
			document.getElementById(id).innerHTML = '<select id=\"worklist_select\" onChange=\"workListProcess(\'worklist_select\', \'' + id + '\'); return true;\">' +
			'<option value=\"\">-</option>'+
		";

		$q = $GLOBALS['sql']->query("SELECT * FROM schedulerstatustype ORDER BY id");
		foreach ($q AS $r) {
			$display_buffer .= "'<option value=\"${r['id']}\">${r['sname']}</option>'+\n";
		}
		$display_buffer .= "
			'<option value=\"\">-</option>'+
			'</select>';
			workListElementActive = 1;
		}

		function workListProcess ( id, parent ) {
			if (! document.getElementById(id).value ) {
				document.getElementById(parent).innerHTML = '&nbsp;';
				workListElementCurrent = '';
				workListElementActive = 0;
				return false;
			}
			x_module_html('".get_class($this)."', 'ajax_process', document.getElementById(id).value + workListElementCurrent, workListPopulate);
		}

		function workListPopulate ( value ) {
			var tokenizer = new StringTokenizer ( value, ':' );
			var _color = tokenizer.nextToken();
			var _text  = tokenizer.nextToken();
			var _desc  = tokenizer.nextToken();
			document.getElementById('r' + workListElementCurrent).style.backgroundColor = _color;
			document.getElementById(workListElementCurrent).innerHTML = '<acronym title=\"' + _desc + '\">' + _text + '</acronym>';
			workListElementCurrent = '';
			workListElementActive = 0;
		}

		</script>
		";

		// Get list of providers
		$p = explode(',', $providers);

		$buffer .= "<table border=\"0\" cellspacing=\"5\"><tr>\n";
		foreach ($p AS $v) {
			$buffer .= "<td valign=\"top\">".$this->generate_worklist( $v )."</td>\n";
		}
		$buffer .= "</tr></table>\n";

		return array (
			__("Work Lists"),
			$buffer
		);
	} // end method notify

	// Method: ProcessChange
	//
	//	Enact a change in scheduler status.
	//
	// Parameters:
	//
	//	$status - Status id
	//
	//	$appt - Scheduler id
	//
	// Returns:
	//
	//	Hash containing:
	//	* color
	//	* name
	//	* descrip
	//
	public function ProcessChange ( $status, $appt ) {
		$this_user = freemed::user_cache();

		// Keep funky things from happening
		if ($status+0 == 0) { return ''; }

		// Actual change
		$a = $GLOBALS['sql']->get_link( 'scheduler', $appt );
		$GLOBALS['sql']->query(
			$GLOBALS['sql']->insert_query(
				'scheduler_status',
				array (
					'csstamp' => SQL__NOW,
					'cspatient' => $a['calpatient'],
					'csappt' => $appt,
					'csstatus' => $status,
					'csuser' => $this_user->user_number
				)
			)
		);

		// Return status color properly
		$r = $GLOBALS['sql']->queryRow("SELECT scolor AS color, sname AS name, sdescrip AS descrip FROM schedulerstatustype WHERE id='".addslashes($status)."'");
		return $r;
	} // end method ProcessChange

	// Method: GenerateWorklists
	//
	//	Retrieve all data associated with provider worklists for
	//	a date.
	//
	// Parameters:
	//
	//	$date - (optional) Date
	//
	// Returns:
	//
	//	Array of array of hashes.
	//
	// SeeAlso:
	//	<GenerateWorklist>
	//
	public function GenerateWorklists ( $date = '' ) {
		$this_user = freemed::user_cache();

		// Handle eventuality of this being a provider
		if ($this_user->isPhysician()) {
			return array ( $this_user->user_phy => $this->GenerateWorklist( $this_user->user_phy, $date ) );
		}

		// TODO: Get providers from user profile
		// FIXME: THIS IS HORRIBLY BROKEN FOR TESTING PURPOSES
		$_providers = array ( 1,2,3 );

		foreach ( $_providers AS $p ) {
			$return[$p] = $this->GenerateWorklist( $p, $date ); 
		}

		return $return;
	} // end method GenerateWorklists

	// Method: GenerateWorklist
	//
	// Parameters:
	//
	//	$provider - Provider id
	//
	//	$date - (optional) Date, defaults to current date
	//
	// Returns:
	//
	public function GenerateWorklist ( $provider, $date = '' ) {
		static $lookup_cache, $s;

		if (!is_object( $s )) {
			$s = CreateObject( 'org.freemedsoftware.api.Scheduler' );
		}

		if ( !$date ) {
			$_date = date('Y-m-d');
		} else {
			$_date = $s->ImportDate( $date );
		}

		// Load lookup table
		if (!isset($lookup_cache)) {
			$lookup_cache = $GLOBALS['sql']->queryAll( "SELECT * FROM schedulerstatustype" );
		}

		foreach ( $lookup_cache AS $r ) {
			$lookup[$r['id']] = $r['scolor'];
			$name_lookup[$r['id']] = $r['sname'];
			$fullname_lookup[$r['id']] = $r['sdescrip'];
			$age_lookup[$r['id']] = $r['sage'];
		}
		unset ($r);

		$pobj = CreateObject( 'org.freemedsoftware.core.Physician', $provider );

		$query = "SELECT s.id AS id, p.id AS s_patient_id, CONCAT(p.ptlname,', ', p.ptfname) AS s_patient, s.calhour AS s_hour, s.calminute AS s_minute, s.calduration AS s_duration, CONCAT(phy.phyfname, ' ', phy.phylname) AS s_provider FROM scheduler s LEFT OUTER JOIN patient p ON p.id=s.calpatient LEFT OUTER JOIN physician phy ON phy.id=s.calphysician WHERE s.caldateof='".addslashes($_date)."' AND s.calphysician='".addslashes($provider)."' AND s.calstatus != 'cancelled' ORDER BY s_hour, s_minute";
		$q = $GLOBALS['sql']->queryAll( $query );
		foreach ( $q AS $r ) {
			$current_status = module_function( 'schedulerpatientstatus', 'getPatientStatus', array( $r['s_patient_id'], $r['id'] ) );
			$expired = false;
			if ($age_lookup[$current_status[0]] > 0 and $current_status[1] >= $age_lookup[$current_status[0]]) {
				syslog(LOG_INFO, "age_lookup ( $current_status[0] ) = ".$age_lookup[$current_status[0]].", current_status[1] = $current_status[1]");
				$expired = true;
			}
		
			$return[] = array (
				'status_name' => $name_lookup[$current_status[0]],
				'status_fullname' => $fullname_lookup[$current_status[0]],
				'status_color' => ( $current_status ? $lookup[$current_status[0]] : "" ),
				'provider' => $r['s_provider'],
				'patient' => $r['s_patient_id'],
				'patient_name' => $r['s_patient'],
				'hour' => $r['s_hour'],
				'minute' => sprintf( '%02d', $r['s_minute'] ),
				'expired' => ( $expired ? true : false )
			);
		} // end get array

		return $return;
	} // end method generate_worklist

} // end class WorkListsModule

register_module('WorkListsModule');

?>
