<?php
/**
 * Project: leads5050-visitor-insights [leads5050-ajax-functions.php]
 * Description: Ajax functions for Leads5050 data retrieval from the Leads5050 Insights Engine
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
 * Retrieve and display the results without using the cta-dashboard-manager.php interface
 *
 * @param $atts attributes
 * @param null $content - the title
 *
 * @return string
 */
function leads5050_visit_report( $atts ) {
	$atts = shortcode_atts(
		array(
			'title' => '',
			'link'  => 'none'
		),
		$atts
	);
	$data = array('type'=>'html');
	$out = '<div class="leads5050-admin-wrap">';
	if ( isset($_POST['rstData']) && is_array($_POST['rstData']['snip']) ){
		$out .= strlen($atts['title'])? '<h2>'.esc_html($atts['title']).'</h2>': '';
		$dat = leads5050_sanitize_array($_POST['rstData']['snip']); // all the elements are escaped by the function
		$out .= '<h3>Visits by Week and Source</h3>';
		//draw the visits table
		if ( isset($dat['source']) && count($dat['source']) ){
			$totalHeaders = array('internal', 'direct', 'external', 'search', 'social', 'advert', 'bot');
			$totalVisits = $dat['source'];
			$out .= '<table class="leads5050-admin-table">';
			$out .= '<thead>';
			$out .= '<tr><th>Week</th>';
			foreach ( $totalHeaders as $hdr ) {
				$out .= '<th class="leads5050d">'.ucfirst($hdr).'</th>';
			}
			$out .= '<th class="leads5050d">Total*</th>';
			$out .= '<th class="leads5050m">Summary*</th>';
			$out .= '</tr>';
			$out .= '</thead><tbody>';
			foreach ( $totalVisits as $weekNum => $arr ) {
				$summary ='';
				$out .= '<tr><td><strong>' . $weekNum . '</strong></td>';
				foreach ( $totalHeaders as $hdr ) {
					$out .= '<td class="leads5050d">'.((isset($arr[$hdr]) && $arr[$hdr]>0)? $arr[$hdr]: '--').'</td>';
					$summary .= '<li>'.ucfirst($hdr).': '.((isset($arr[$hdr]) && $arr[$hdr]>0)? $arr[$hdr]: 0).'</li>';
				}
				$total = $arr['internal']+$arr['external']+$arr['search']+$arr['social']+$arr['advert']+$arr['bot']+$arr['direct'];
				$summary .= '<li><strong>Total:</strong> '.($total>0? $total: 0).'</li>';
				$out .= '<td class="leads5050d">' . ($total>0? $total: '--') . '</td>';
				$out .= '<td class="leads5050m">'.$summary.'</td>';
				$out .= '</tr>';
			}
			$out .= '</tbody>';
			$out .= '</table>';
			$out .= '<p><em>* Total includes visits from unauthorised or Spoof Bots</em></p>';
		} else {
			$out .= '<p>Visit information not yet available for this site</p>';
		}
		//draw the social visits by week table
		$out .= '<h3>Social Visits by Week</h3>';
		if ( isset($dat['social']) && count($dat['social']) ){
			$socialHeaders = array('FBK'=>'Facebook', 'TWT'=>'Twitter', 'PIN'=>'Pinterest', 'LIN'=>'LinkedIn', 'INS'=>'Instagram', 'WAP'=>'WhatsApp');
			$socialVisits = $dat['social'];
			$out .= '<table class="leads5050-admin-table">';
			$out .= '<thead>';
			$out .= '<tr><th>Week</th>';
			foreach ( $socialHeaders as $lbl=>$hdr ) {
				$out .= '<th class="leads5050d"><span title="'.$hdr.'">' . $lbl . '</span></th>';
			}
			$out .= '<th class="leads5050m">Summary</th>';
			$out .= '</tr>';
			$out .= '</thead><tbody>';
			foreach ( $socialVisits as $weekNum => $arr ) {
				$summary ='';
				$out .= '<tr><td><strong>' . $weekNum . '</strong></td>';
				foreach ( $socialHeaders as $hdr ) {
					$out .= '<td class="leads5050d">'.((isset($arr[$hdr]) && $arr[$hdr]>0)? number_format($arr[$hdr],0): 0).'</td>';
					$summary .= '<li>'.ucfirst($hdr).': '.((isset($arr[$hdr]) && $arr[$hdr]>0)? $arr[$hdr]: '-').'</li>';
				}
				$out .= '<td class="leads5050m">'.$summary.'</td>';
				$out .= '</tr>';
			}
			$out .= '</tbody>';
			$out .= '</table>';
		} else {
			$out .= '<p>Social visit information not yet available for this site</p>';
		}
		//draw the backlinks table
		$out .= '<h3>Backlinks Found</h3>';
		if ( isset($dat['backlinks']) && count($dat['backlinks']) ){
			$blinkHeaders = array('f'=>'First Detected', 'l'=>'Latest Occurrence', 'n'=>'Total Visits');
			$backlinks = $dat['backlinks'];
			$out .= '<table class="leads5050-admin-table">';
			$out .= '<thead>';
			$out .= '<tr><th>Link</th>';
			foreach ( $blinkHeaders as $lbl=>$hdr ) {
				$out .= '<th class="leads5050d">' . $hdr . '</th>';
			}
			$out .= '<th class="leads5050d">Visits Per Day</th>';
			$out .= '<th class="leads5050m">Summary</th>';
			$out .= '</tr>';
			$out .= '</thead><tbody>';
			foreach ( $backlinks as $backlink => $arr ) {
				$summary ='';
				$backlink = esc_url($backlink);
				$linkText = strlen($backlink)>40? substr($backlink, 0, 40).'...': $backlink;
				if ( strtolower( $atts['link'] ) == 'follow' || strtolower( $atts['link'] ) == 'nofollow' ) {
					$lnk0 = '<a href="'.$backlink.'" '.( strtolower($atts['link'])=='nofollow'? 'rel="nofollow"': '' ).
					        ' target="_blank">'.$linkText.'</a>';
				} else {
					$lnk0 = '<span title="' . $backlink . '">'. $linkText . '</span>';
				}
				$out .= '<tr>';
				$out .= '<td><strong>'.$lnk0.'</strong></td>';
				foreach ( $blinkHeaders as $lbl=>$hdr ) {
					$out .= '<td class="leads5050d">'.($lbl=='n'? $arr['n']: date('Y-m-d', $arr[$lbl])).'</td>';
					$summary .= '<li>'.$hdr.': '.($lbl=='n'? $arr['n']: date('Y-m-d', $arr[$lbl])).'</li>';
				}
				$vpd = (time()-$arr['f'])/(86400);
				$vpd = $arr['n']/($vpd > 1? $vpd: 1);
				$out .= '<td class="leads5050d">'.number_format($vpd,2).'</td>';
				$summary .= '<li>Visits per Day: '.number_format($vpd,2).'</li>';
				$out .= '<td class="leads5050m">'.$summary.'</td>';
				$out .= '</tr>';
			}
			$out .= '</tbody>';
			$out .= '</table>';
		} else {
			$out .= '<p>Backlink information not yet available for this site</p>';
		}
	} else {
		$out .= '<h4>ERROR</h4><p>The data was not retrieved</p>';
	}
	$out .= '</div>';
	$data['output'] = $out;
	echo json_encode($data);
	wp_die(); //NB!! to prevent the whitespace error
}

