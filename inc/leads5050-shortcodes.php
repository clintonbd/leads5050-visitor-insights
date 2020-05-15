<?php
/**
 * Project: leads5050-visitor-insights [leads5050-shortcodes.php]
 * Description: Shortcodes for the Leads5050 Insights Engine - FUTURE VERSION SHORTCODES
 * potential leads and new customers
 * Author: Clinton [Leads5050]
 * License: GPLv3
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
 *
 */

/**
 * Create a basic chart - for later version of this plugin
 *
 * @param $atts attributes
 * @param null $content - the title
 *
 * @return string
 */
function leads5050_template_basic( $atts, $content=null ) {
	$atts = shortcode_atts(
		array(
			'chart_id' => 'myChart',
			'title' => 'DEBUG INFORMATION',
			'type' => 'chart'
		),
		$atts
	);
	$out = '';
	$allowedCharts = array('myChart', 'lineChart', 'barChart', 'timelineChart');
	$atts['chart_id'] = in_array($atts['chart_id'],$allowedCharts)? $atts['chart_id']: 'myChartX';
	$atts['type'] = strtolower(in_array($atts['type'],array('chart', 'table'))? $atts['api']: 'chart');
	$title = (is_null($content) || strlen($content)==0)? $atts['title']: $content;
	$out .= '<h2>'.$title.'</h2>';
	//$out .= '<pre>'.var_export($atts,true).'</pre>';
	$out .= '<div class="ract_container">';
		$out .= '<canvas id="'.$atts['chart_id'].'"></canvas>';
		$out .= '<canvas id="timeChart"></canvas>';
		$out .= '<canvas id="scatterChart"></canvas>';
		$out .= '<canvas id="xyChart"></canvas>';
		$out .= '<canvas id="myChart"></canvas>';
	$out .= '</div>';

	return $out;
}
add_shortcode( 'leads5050_template_basic', 'leads5050_template_basic' );
