<?php

/////// Created by Greenreader9 on GitHub https://github.com/greenreader9
// Licensed under MIT

// Copyright (c) 2024, Greenreader9
//
// Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
// to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
// and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

namespace Greenreader9;

class NameSiloAPI{
	private $apikey;
	private $ua;
	private $normalURL = 'https://www.namesilo.com/api/'; // final
	private $bulkURL = 'https://www.namesilo.com/apibatch/'; // final
	private $currURL;

	private $lastHTTP = NULL;
	private $lastResult = NULL;
	private $lastEndpoint = NULL;
	private $lastURL = null;

	///////// Configure the Class //////////////

	// An API key, User Agent MUST be provided when the class is called. 
	// These can be changed later by calling setKey() and setUA() respectively 
	// A third param can be set to 'bulk' to use the bulk API. Set it to any other value for the normal url
	function __construct($apiKey, $userAgent, $apiType='normal'){
		$this->apikey = $apiKey;
		$this->ua = $userAgent;

		if($apiType == 'bulk'){
			$this->currURL = $this->bulkURL;
		} else{
			$this->currURL = $this->normalURL;
		}
	}

	// Set your API key --REQUIRED
	function setKey($key){
		$this->apikey = $key;
	}

	// Set your UA --REQUIRED
	function setUA($userAgent){
		$this->us = $userAgent;
	}

	// change the URL type
	// pass 'bulk' to use bulk URL. Ignore param or set it to any other value for the normal url
	function setAPIType($apiType='normal'){
		if($apiType == 'bulk'){
			$this->currURL = $this->bulkURL;
		} else{
			$this->currURL = $this->normalURL;
		}
	}


	//////////// Get information on the last API call under this instance //////////////

	// returns last HTTP code, or NULL (If no API requests have been made)
	function getHTTPCode(){
		return $this->lastHTTP;
	}
	// returns last HTTP body response, or NULL (If no API requests have been made)
	function getLastResult(){
		return $this->lastResult;
	}
	// returns last endpoint called, or NULL (If no API requests have been made)
	function getLastCall(){
		return $this->lastEndpoint;
	}
	// returns last URL called, or NULL (If no API requests have been made) --WARNING:::: EXPOSES PRIVATE API KEY!!!!
	// NEVER USE IN PRODUCTION!
	function getLastURL(){
		return $this->lastURL;
	}


	/////////// Internal functions ///////////////

	private function checkSetup(){
		if(!empty($apikey) || !empty($userAgent)){
			return false;
		} else{
			return true;
		}
	}

	private function returnBAD($type){
		if($type == 'setup'){
			return '<namesilo><request><operation>API CALL</operation><ip>0.0.0.0</ip></request><reply><code>100</code><detail>API KEY OR USER AGENT NOT CONFIGURED IN API CLIENT</detail></reply></namesilo>';
		} elseif($type == 'reqBulk'){
			return '<namesilo><request><operation>API CALL</operation><ip>0.0.0.0</ip></request><reply><code>100</code><detail>BULK API MUST BE USED FOR THIS COMMAND - SET WITH setAPIType("bulk") - API CLIENT ERROR</detail></reply></namesilo>';
		} elseif($type == 'nosupport'){
			return '<namesilo><request><operation>API CALL</operation><ip>0.0.0.0</ip></request><reply><code>100</code><detail>API ENDPOINT NOT SUPPORTED BY CLIENT - API CLIENT ERROR</detail></reply></namesilo>';
		} else{
			return '<namesilo><request><operation>API CALL</operation><ip>0.0.0.0</ip></request><reply><code>100</code><detail>UNKNOWN API CLIENT ERROR</detail></reply></namesilo>';
		}
	}

	private function cURLCall($endpoint, $additonalParams=''){
		$this->lastEndpoint = $endpoint;
		if(!$this->checkSetup()){return $this->returnBAD('setup');}

		$url = $this->currURL.$endpoint."?version=1&type=xml&key=".$this->apikey.$additonalParams;
		$this->lastURL = $url;

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt ($ch, CURLOPT_USERAGENT, $this->ua);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt ($ch, CURLOPT_HEADER, false);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 6);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$HTTPBodyresult = simplexml_load_string(curl_exec($ch));
		$this->lastResult = $HTTPBodyresult;
		$this->lastHTTP = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return $HTTPBodyresult;
	}

	function buildParam($array) {
	    $queryParams = [];
	    foreach ($array as $key => $value) {
	        $queryParams[] = urlencode($key) . '=' . urlencode($value);
	    }
	    return '&' . implode('&', $queryParams);
	}

	//////////// Domains ///////////////

	// https://www.namesilo.com/api-reference#domains/register-domain
	// REQUIRED: domain, years
	function registerDomain($domain, $years, $payment_id=null, $private=null, $auto_renew=null, $portfolio=null, $ns1=null, $ns2=null, $ns3=null, $ns4=null, $ns5=null, $ns6=null, $ns7=null, $ns8=null, $ns9=null, $ns10=null, $ns11=null, $ns12=null, $ns13=null, $coupon=null, $fn=null, $ln=null, $ad=null, $cy=null, $st=null, $zp=null, $ct=null, $em=null, $ph=null, $nn=null, $cp=null, $ad2=null, $fx=null, $usnc=null, $usap=null, $contact_id=null, $submit_claims=null, $submit_date=null){

		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('registerDomain', $this->buildParam($filteredVariables));
	}


	// https://www.namesilo.com/api-reference#domains/register-domain-drop
	// domain, years required
	function registerDomainDrop($domain, $years, $private=null, $auto_renew=null){

		if($this->currURL != $this->bulkURL){
			return $this->returnBAD('reqBulk');
		}

		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('registerDomainDrop', $this->buildParam($filteredVariables));
	}


	// https://www.namesilo.com/api-reference#domains/renew-domain
	// domain, years required
	function renewDomain($domain, $years, $payment_id=null, $coupon=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('renewDomain', $this->buildParam($filteredVariables));
	}

	//https://www.namesilo.com/api-reference#domains/transfer-domain
	// domain required
	function transferDomain($domain, $payment_id=null, $auth=null, $private=null, $auto_renew=null, $portfolio=null, $coupon=null, $fn=null, $ln=null, $ad=null, $cy=null, $st=null, $zp=null, $ct=null, $em=null, $ph=null, $nn=null, $cp=null, $ad2=null, $fx=null, $usnc=null, $usap=null, $contact_id=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('transferDomain', $this->buildParam($filteredVariables));
	}

	//https://www.namesilo.com/api-reference#domains/list-domains
	// none required
	function listDomains($portfolio=null, $pageSize=null, $page=null, $withBid=null, $skipExpired=null, $expiredGrace=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('listDomains', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#domains/get-domain-info
	// domain required
	function getDomainInfo($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('getDomainInfo', $this->buildParam($filteredVariables));
	}

	//https://www.namesilo.com/api-reference#account/get-prices
	// none required
	function getPrices($retail_prices=null, $registration_domains=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('getPrices', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#domains/domain-forward
	// first 4 required
	function domainForward($domain, $protocol, $address, $method, $meta_title=null, $meta_description=null, $meta_keywords=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('domainForward', $this->buildParam($filteredVariables));
	}

	//https://www.namesilo.com/api-reference#domains/domain-forward-sub-domain
	// first 5 required
	function domainForwardSubDomain($domain, $sub_domain, $protocol, $address, $method, $meta_title=null, $meta_description=null, $meta_keywords=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('domainForwardSubDomain', $this->buildParam($filteredVariables));
	}

	//https://www.namesilo.com/api-reference#domains/domain-forward-sub-domain-delete
	// all required
	function domainForwardSubDomainDelete($domain, $sub_domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('domainForwardSubDomainDelete', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#domains/add-auto-renewal
	// all required
	function addAutoRenewal($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('addAutoRenewal', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#domains/remove-auto-renewal
	// all required
	function removeAutoRenewal($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('removeAutoRenewal', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#domains/domain-lock
	// all required
	function domainLock($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('domainLock', $this->buildParam($filteredVariables));
	}


	//https://www.namesilo.com/api-reference#domains/domain-unlock
	// all required
	function domainUnlock($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('domainUnlock', $this->buildParam($filteredVariables));
	}

	//https://www.namesilo.com/api-reference#domains/check-register-availability
	// all required
	function checkRegisterAvailability($domains){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('checkRegisterAvailability', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#domains/check-transfer-availability
	// all required
	function checkTransferAvailability($domains){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('checkTransferAvailability', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#domains/whois
	// all required
	function whoisInfo($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('domainUnlock', $this->buildParam($filteredVariables));
	}


	///////////////////// Domain Transfers /////////////////

	// more functions under domains header

	//https://www.namesilo.com/api-reference#transfers/check-transfer-status
	// all required
	function checkTransferStatus($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('checkTransferStatus', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#transfers/transfer-update-change-epp-code
	// all required
	function transferUpdateChangeEPPCode($domain, $auth){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('transferUpdateChangeEPPCode', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#transfers/transfer-update-resend-admin-email
	// all required
	function transferUpdateResendAdminEmail($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('transferUpdateResendAdminEmail', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#transfers/transfer-update-resubmit-registry
	// all required
	function transferUpdateResubmitToRegistry($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('transferUpdateResubmitToRegistry', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#transfers/retrieve-auth-code
	// all required
	function retrieveAuthCode($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('retrieveAuthCode', $this->buildParam($filteredVariables));
	}


	///////////////////// Contact /////////////////

	// https://www.namesilo.com/api-reference#contact/contact-list
	// all required
	function contactList($contact_id, $offset){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('contactList', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#contact/contact-add
	// conditionally required
	function contactAdd($fn=null, $ln=null, $ad=null, $cy=null, $st=null, $zp=null, $ct=null, $em=null, $ph=null, $nn=null, $cp=null, $ad2=null, $fx=null, $usnc=null, $usap=null, $calf=null, $caln=null, $caag=null, $cawd=null, $eucs=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('contactAdd', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#contact/contact-update
	// conditionally required
	function contactUpdate($contact_id=null, $fn=null, $ln=null, $ad=null, $cy=null, $st=null, $zp=null, $ct=null, $em=null, $ph=null, $nn=null, $cp=null, $ad2=null, $fx=null, $usnc=null, $usap=null, $calf=null, $caln=null, $caag=null, $cawd=null, $eucs=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('contactUpdate', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#contact/contact-delete
	// all required
	function contactDelete($contact_id){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('contactDelete', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#contact/contact-domain-associate
	// first required
	function contactDomainAssociate($domain, $registrant=null, $administrative=null, $billing=null, $technical=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('contactDomainAssociate', $this->buildParam($filteredVariables));
	}



	////////////////////// Nameserver //////////////

	// https://www.namesilo.com/api-reference#nameserver/change-nameserver
	// first 3 required 
	function changeNameServers($domain, $ns1, $ns2, $ns3=null, $ns4=null, $ns5=null, $ns6=null, $ns7=null, $ns8=null, $ns9=null, $ns10=null, $ns11=null, $ns12=null, $ns13=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('changeNameServers', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#nameserver/list-registered-nameservers
	// all required
	function listRegisteredNameServers($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('listRegisteredNameServers', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#nameserver/add-registered-nameserver
	// first 2 required
	function addRegisteredNameServer($new_host, $ip1, $ip2=null, $ip3=null, $ip4=null, $ip5=null, $ip6=null, $ip7=null, $ip8=null, $ip9=null, $ip10=null, $ip11=null, $ip12=null, $ip13=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('addRegisteredNameServer', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#nameserver/modify-registered-nameserver
	// first 3 required
	function modifyRegisteredNameServer($current_host, $new_host, $ip1, $ip2=null, $ip3=null, $ip4=null, $ip5=null, $ip6=null, $ip7=null, $ip8=null, $ip9=null, $ip10=null, $ip11=null, $ip12=null, $ip13=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('modifyRegisteredNameServer', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#nameserver/delete-registered-nameserver
	// all required
	function deleteRegisteredNameServer($current_host){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('deleteRegisteredNameServer', $this->buildParam($filteredVariables));
	}


	////////////////////////////// DNS /////////////////////////////

	// https://www.namesilo.com/api-reference#dns/dns-list-records
	// all required
	function dnsListRecords($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('dnsListRecords', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#dns/dns-add-record
	// first 4 required
	function dnsAddRecord($domain, $rrtype, $rrhost, $rrvalue, $rrdistance=null, $rrttl=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('dnsAddRecord', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#dns/dns-update-record
	// first 4 required
	function dnsUpdateRecord($domain, $rrid, $rrhost, $rrvalue, $rrdistance=null, $rrttl=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('dnsUpdateRecord', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#dns/dns-delete-record
	// all required
	function dnsDeleteRecord($domain, $rrid){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('dnsDeleteRecord', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#dns/dns-seclist-records
	// all required
	function dnsSecListRecords($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('dnsSecListRecords', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#dns/dns-secadd-records
	// all required
	function dnsSecAddRecord($domain, $digest, $keyTag, $digestType, $alg){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('dnsSecAddRecord', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#dns/dns-secdelete-record
	// all required
	function dnsSecDeleteRecord($domain, $digest, $keyTag, $digestType, $alg){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('dnsSecDeleteRecord', $this->buildParam($filteredVariables));
	}


	////////////////////////// Portfolio ///////////////////////

	// https://www.namesilo.com/api-reference#portfolio/portfolio-list
	// none
	function portfolioList(){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('portfolioList', $this->buildParam($filteredVariables));
	}
 
	// https://www.namesilo.com/api-reference#portfolio/portfolio-add
	// all required
	function portfolioAdd($portfolio){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('portfolioAdd', $this->buildParam($filteredVariables));

	}

	// https://www.namesilo.com/api-reference#portfolio/portfolio-delete
	// all required
	function portfolioDelete($portfolio){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('portfolioDelete', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#portfolio/portfolio-domain-associate
	// all required
	function portfolioDomainAssociate($portfolio, $domains){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('portfolioDomainAssociate', $this->buildParam($filteredVariables));
	}


	////////////////////// Privacy ///////////////////

	//https://www.namesilo.com/api-reference#portfolio/portfolio-domain-associate
	// all required
	function addPrivacy($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('addPrivacy', $this->buildParam($filteredVariables));
	}

	//https://www.namesilo.com/api-reference#privacy/remove-privacy
	// all required
	function removePrivacy($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('removePrivacy', $this->buildParam($filteredVariables));
	}

	////////////////////////// Email /////////////////////

	// https://www.namesilo.com/api-reference#email/list-email-forwards
	// all required
	function listEmailForwards($domain){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('listEmailForwards', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#email/configure-email-forward
	// first 3 required
	function configureEmailForward($domain, $email, $forward1, $forward2=null, $forward3=null, $forward4=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('configureEmailForward', $this->buildParam($filteredVariables));
	}

	//https://www.namesilo.com/api-reference#email/delete-email-forward
	// all required
	function deleteEmailForward($domain, $email){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('deleteEmailForward', $this->buildParam($filteredVariables));
	} 

	// https://www.namesilo.com/api-reference#email/registrant-verification-status
	// all required
	function registrantVerificationStatus($email){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('registrantVerificationStatus', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#email/email-verification
	// all required
	function emailVerification($email){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('emailVerification', $this->buildParam($filteredVariables));
	}

	// more functions under domain transfers header

	/////////////////////// Marketplace ///////////////

	// https://www.namesilo.com/api-reference#marketplace/marketplace-active-sales-overview
	// none
	function marketplaceActiveSalesOverview(){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('marketplaceActiveSalesOverview', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#marketplace/marketplace-add-modify-sale
	// first 3 required
	function marketplaceAddOrModifySale($domain, $action, $sale_type, $reserve=null, $show_reserve=null, $allow_bids_under_reserve=null, $buy_now=null, $payment_plan_offered=null, $payment_plan_months=null, $payment_plan_down_payment=null, $end_date=null, $end_date_use_maximum=null, $notify_buyers=null, $category1=null, $description=null, $use_for_sale_landing_page=null, $mp_use_our_nameservers=null, $password=null, $cancel_sale=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('marketplaceAddOrModifySale', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#marketplace/marketplace-landing-page-update
	// domain required
	function marketplaceLandingPageUpdate($domain, $mp_template=null, $mp_bgcolor=null, $mp_textcolor=null, $mp_show_buy_now=null, $mp_show_more_info=null, $mp_show_renewal_price=null, $mp_show_other_for_sale=null, $mp_other_domain_links=null, $mp_message=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('marketplaceLandingPageUpdate', $this->buildParam($filteredVariables));
	}


	//////////////////// Forwarding ///////////////////

	// other forwarding function are under domains header

	// other forwarding functions are under mail header



	//////////////////// Account //////////////////////


	// https://www.namesilo.com/api-reference#account/get-account-balance
	// none
	function getAccountBalance(){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('getAccountBalance', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#account/add-account-funds
	// all required
	function addAccountFunds($amount, $payment_id){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('addAccountFunds', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#account/list-orders
	// none
	function listOrders(){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('listOrders', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#account/order-details
	// all required
	function orderDetails($order_number){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('orderDetails', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#account/list-expiring-domains
	// daysCount required
	function listExpiringDomains($daysCount, $page=null, $pageSize=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('listExpiringDomains', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#account/count-expiring-domains
	// all required
	function countExpiringDomains($daysCount){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('countExpiringDomains', $this->buildParam($filteredVariables));
	}



	/////////////////////// Autions ///////////////////

	// https://www.namesilo.com/api-reference#auctions/list-auctions
	// none required
	function listAuctions($domainId=null, $domainName=null, $typeId=null, $statusId=null, $buyNow=null, $minCurrentBid=null, $maxCurrentBid=null, $orderBy=null, $orderType=null, $page=null, $pageSize=null, $watchlist=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('listAuctions', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#auctions/view-auction
	// all required
	function viewAuction($auctionId){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('viewAuction', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#auctions/view-auctions
	// all required
	function viewAuctions($auctionIds){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('viewAuctions', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#auctions/watch-auction
	// all required
	function watchAuction($auctionId, $watch){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('watchAuction', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#auctions/bid-auction
	// first 2 required
	function bidAuction($auctionId, $bid, $proxyBid=null){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('bidAuction', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#auctions/bulk-bid-auction
	// NOT SUPPORTED!
	function bidAuctions(){
		return $this->returnBAD('nosupport');
	}

	// https://www.namesilo.com/api-reference#auctions/buy-now-auction
	// all required
	function buyNowAuction($auctionId){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('buyNowAuction', $this->buildParam($filteredVariables));
	}

	// https://www.namesilo.com/api-reference#auctions/view-auction-history
	// all required
	function viewAuctionHistory($auctionId){
		$variables = get_defined_vars();
		
		$filteredVariables = array_filter($variables, function($value) {
		    return $value !== null;
		});

		return $this->cURLCall('viewAuctionHistory', $this->buildParam($filteredVariables));
	}
}

?>
