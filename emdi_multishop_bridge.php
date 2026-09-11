<?php
/*------------------------------------------------------------------------
# EMDI - multishop bridge by SBZ systems - Solon Zenetzis - version 2
# ------------------------------------------------------------------------
# Aggregates two (or more) shop bridges into one endpoint for EMDI.
# EMDI Settings → Internet links: point domain/key at THIS script like a normal bridge.
# ------------------------------------------------------------------------
# author    SBZ systems - Solon Zenetzis
# copyright Copyright (C) 2020-2026 sbzsystems.com. All Rights Reserved.
# @license - https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.sbzsystems.com
-------------------------------------------------------------------------*/
header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: text/html; charset=UTF-8');
error_reporting(0);

// --- configure ---
$passkey = 'CHANGE_ME'; // must match the key=… EMDI sends

// Each shop: full bridge URL including its own key= (without action)
$eshops = array(
	array(
		'url' => 'https://eshop1.gr/emdi_wp_woo_bridge.php?company=ike&key=12245325',
		'order_prefix' => '', // main shop: no order-id prefix
	),
	array(
		'url' => 'https://eshop2.com/emdi_open2_bridge.php?key=235232354235',
		'order_prefix' => 'EM', // unique prefix; also set in that shop bridge when echoing order ids
	),
);

// --- request ---
$productid = isset($_REQUEST['productid']) ? $_REQUEST['productid'] : '';
if ($productid !== '' && function_exists('mb_check_encoding') && !mb_check_encoding($productid, 'UTF-8')) {
	$productid = @iconv('ISO-8859-7', 'UTF-8//IGNORE', $productid);
}
$stock = isset($_REQUEST['stock']) ? $_REQUEST['stock'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$orderid = isset($_REQUEST['orderid']) ? $_REQUEST['orderid'] : '';
$key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
$shipcomp = isset($_REQUEST['shipcomp']) ? $_REQUEST['shipcomp'] : '';
$voucherno = isset($_REQUEST['voucherno']) ? $_REQUEST['voucherno'] : '';
$docid = isset($_REQUEST['docid']) ? $_REQUEST['docid'] : '';

if ($key !== $passkey) {
	exit;
}

function emdi_ms_fetch($url)
{
	$ctx = stream_context_create(array(
		'http' => array('timeout' => 60, 'ignore_errors' => true),
		'ssl' => array('verify_peer' => true, 'verify_peer_name' => true),
	));
	$out = @file_get_contents($url, false, $ctx);
	return ($out === false) ? '' : $out;
}

function emdi_ms_shop_url($base, $query)
{
	$sep = (strpos($base, '?') === false) ? '?' : '&';
	return $base . $sep . 'rndval=' . rand(10000000, 90000000) . '&' . $query;
}

function emdi_ms_strip_header_row($csv)
{
	return preg_replace('/^.+(\r\n|\n|\r)/', '', $csv, 1);
}

function emdi_ms_find_shop($eshops, $orderid)
{
	// Longest matching prefix wins (empty prefix = fallback / main shop)
	$best = null;
	$bestLen = -1;
	foreach ($eshops as $shop) {
		$p = isset($shop['order_prefix']) ? (string)$shop['order_prefix'] : '';
		if ($p === '') {
			if ($best === null) {
				$best = $shop;
				$bestLen = 0;
			}
			continue;
		}
		if (mb_stripos($orderid, $p) === 0 && mb_strlen($p) > $bestLen) {
			$best = $shop;
			$bestLen = mb_strlen($p);
		}
	}
	return $best;
}

function emdi_ms_orderid_for_shop($orderid, $shop)
{
	$p = isset($shop['order_prefix']) ? (string)$shop['order_prefix'] : '';
	if ($p === '') {
		return $orderid;
	}
	if (mb_stripos($orderid, $p) === 0) {
		return mb_substr($orderid, mb_strlen($p));
	}
	return str_ireplace($p, '', $orderid);
}

if ($action === 'deletetmp' || $action === 'customersok' || $action === 'productsok' || $action === 'updatestock') {
	foreach ($eshops as $shop) {
		$q = 'action=' . rawurlencode($action);
		if ($action === 'updatestock') {
			$q .= '&productid=' . rawurlencode($productid) . '&stock=' . rawurlencode($stock);
		}
		echo emdi_ms_fetch(emdi_ms_shop_url($shop['url'], $q));
	}
	exit;
}

if ($action === 'customers' || $action === 'products' || $action === 'orders') {
	$first = true;
	foreach ($eshops as $shop) {
		$raw = emdi_ms_fetch(emdi_ms_shop_url($shop['url'], 'action=' . rawurlencode($action)));
		if ($first) {
			echo $raw;
			$first = false;
		} else {
			echo emdi_ms_strip_header_row($raw);
		}
	}
	exit;
}

if ($action === 'order' || $action === 'confirmorder' || $action === 'cancelorder') {
	$shop = emdi_ms_find_shop($eshops, $orderid);
	if ($shop === null) {
		exit;
	}
	$oid = emdi_ms_orderid_for_shop($orderid, $shop);
	$q = 'action=' . rawurlencode($action) . '&orderid=' . rawurlencode($oid);
	if ($action === 'confirmorder') {
		$q .= '&docid=' . rawurlencode($docid)
			. '&shipcomp=' . rawurlencode($shipcomp)
			. '&voucherno=' . rawurlencode($voucherno);
	}
	echo emdi_ms_fetch(emdi_ms_shop_url($shop['url'], $q));
	exit;
}
?>
