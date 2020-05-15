<?php
/**
 * Plugin Name: Leads 5050 Visitor Insights
 * Plugin URI:  https://leads5050.com/wordpress-plugin/
 * Description: Leads5050 records and monitors visits to your website providing insights into
 * potential leads and new customers
 * Version:     1.0
 * Author:		Clinton [Leads5050]
 * Author URI: https://leads5050.com
 * License: GPLv3
 * Last change: 2020-05-07
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

//Security - abort if this file is called directly
if (!defined('WPINC')){
	die;
}

define( 'LREFI_ROOT', __FILE__ );
define( 'LREFI_DIR', plugin_dir_path( __FILE__ ) );
require_once( LREFI_DIR . 'class.leads5050-visitor-insights.php');

$pgf = new leads5050(__FILE__);