<?php
/**
 * Description: Runs on uninstall to remove any traces of the plugin
 * Author: Clinton [Leads5050]
 * License: GPLv3
 *
 * Copyright 2020 CreatorSEO (email : info@creatorseo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You can find a copy of the GNU General Public License at the link
 * http://www.gnu.org/licenses/gpl.html or write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Check that we should be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if accessed directly
}

// Delete Options
$options = array(
	'leads5050_options',
	'leads5050_log',
	'leads5050_code'
);
foreach ( $options as $option ) {
	if ( get_option( $option ) ) {
		delete_option( $option );
	}
}


