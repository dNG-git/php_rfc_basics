<?php
//j// BOF

/*n// NOTE
----------------------------------------------------------------------------
RFC Basics for PHP
----------------------------------------------------------------------------
(C) direct Netware Group - All rights reserved
http://www.direct-netware.de/redirect.php?php;rfc_basics

This Source Code Form is subject to the terms of the Mozilla Public License,
v. 2.0. If a copy of the MPL was not distributed with this file, You can
obtain one at http://mozilla.org/MPL/2.0/.
----------------------------------------------------------------------------
http://www.direct-netware.de/redirect.php?licenses;mpl2
----------------------------------------------------------------------------
#echo(phpRfcBasicsVersion)#
#echo(__FILEPATH__)#
----------------------------------------------------------------------------
NOTE_END //n*/
/**
* This class provides functions to handle server based communication for
* downloading or uploading content.
*
* @internal  We are using ApiGen to automate the documentation process for
*            creating the Developer's Manual. All sections including these
*            special comments will be removed from the release source code.
*            Use the following line to ensure 76 character sizes:
* ----------------------------------------------------------------------------
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   rfc_basics.php
* @since     v0.1.00
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
/*#ifdef(PHP5n) */

namespace dNG\rfc;
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

/**
* This support is a basic one. You can use fopen as well as GET and POST
* commands (depending on the "socket" constant).
*
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   rfc_basics.php
* @since     v0.1.00
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
class directHttp extends directBasics
{
/**
	* @var string $content_type Content type
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $content_type = NULL;
/**
	* @var string $curl_ptr CURL pointer
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $curl_ptr = NULL;
/**
	* @var array $data Raw data received
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $data = "";
/**
	* @var array $data_http_headers Cached headers of an HTTP call
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $data_http_headers = array();
/**
	* @var string $data_http_result_code The result code of an HTTP call
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $data_http_result_code = "";
/**
	* @var boolean $PHP_curl_init True if the PHP function "curl_init()" is
	*      supported.
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $PHP_curl_init;
/**
	* @var boolean $PHP_stream_select True if the PHP function "stream_select() "
	*      is supported.
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $PHP_stream_select;
/**
	* @var string $timeout_connection Connection timeout
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $timeout_connection = 3;
/**
	* @var string $timeout_data Data timeout
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $timeout_data = 30;

/* -------------------------------------------------------------------------
Extend the class using old and new behavior
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directHttp)
	*
	* @param object $event_handler EventHandler to use
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function __construct($event_handler = NULL)
	{
		parent::__construct($event_handler);

		if (!defined("USE_socket")) { define("USE_socket", function_exists("fsockopen")); }
		$this->PHP_curl_init = (defined("USE_curl") ? USE_curl : function_exists("curl_init"));
		$this->PHP_stream_select = function_exists("stream_select");
	}
/*#ifdef(PHP4):
/**
	* Constructor (PHP4) directHttp
	*
	* @param object $event_handler EventHandler to use
	* @since v0.1.00
*\/
	function directHttp($event_handler = NULL) { $this->__construct($event_handler); }
:#*/
/**
	* Does an HTTP request using CURL.
	*
	* @param  string $request Request type
	* @param  string $server Server name or IP address of target
	* @param  integer $port The target port
	* @param  string $query Query string
	* @param  mixed $data Variables that should be transfered to the remote
	*         server (empty string for none)
	* @param  boolean $header_only Only receive the header
	* @param  integer $byte_first First byte to be read
	* @param  mixed $byte_last Last byte to be read (empty for EOF)
	* @return mixed Remote content on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function curlRequest($request, $server, $port = 80, $query = "", $data = NULL, $header_only = false, $byte_first = "", $byte_last = "")
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->curlRequest($request, $server, $port, $query, +data, +header_only, $byte_first, $byte_last)- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_int($byte_first)) { $range = (is_int($byte_last) ? $byte_first."-".$byte_last : $byte_first."-"); }
		else { $range = NULL; }

		if (preg_match("#^http(s|):\/\/(.+?)$#i", $server) && defined("CURLOPT_CONNECTTIMEOUT") && defined("CURLOPT_HEADER") && defined("CURLOPT_HTTPHEADER") && defined("CURLOPT_RETURNTRANSFER") && defined("CURLOPT_TIMEOUT"))
		{
			if ($this->curl_ptr === NULL) { $this->curl_ptr = curl_init($server.":".$port.$query); }
			else { curl_setopt($this->curl_ptr, CURLOPT_URL, $server.":".$port.$query); }

			$curl_close = true;

			if ($this->curl_ptr)
			{
				if (defined("CURLOPT_ACCEPT_ENCODING")) { curl_setopt($this->curl_ptr, CURLOPT_ACCEPT_ENCODING, ""); }
				elseif (defined("CURLOPT_ENCODING")) { curl_setopt($this->curl_ptr, CURLOPT_ENCODING, ""); }

				if (defined("CURLOPT_BINARYTRANSFER")) { curl_setopt($this->curl_ptr, CURLOPT_BINARYTRANSFER, true); }
				curl_setopt($this->curl_ptr, CURLOPT_CONNECTTIMEOUT, $this->timeout_connection);
				curl_setopt($this->curl_ptr, CURLOPT_HEADER, true);
				if ((isset($range))&&(defined("CURLOPT_RANGE"))) { curl_setopt($this->curl_ptr, CURLOPT_RANGE, $range); }

				if (defined("CURLOPT_CUSTOMREQUEST")) { curl_setopt($this->curl_ptr, CURLOPT_CUSTOMREQUEST, $request); }
				elseif (isset($data) && defined("CURLOPT_HTTPPOST")) { curl_setopt($this->curl_ptr, CURLOPT_HTTPPOST, true); }
				elseif (defined("CURLOPT_HTTPGET")) { curl_setopt($this->curl_ptr, CURLOPT_HTTPGET, true); }

				if ($header_only && defined("CURLOPT_NOBODY")) { curl_setopt($this->curl_ptr, CURLOPT_NOBODY, true); }
				if (defined("CURLOPT_NOPROGRESS")) { curl_setopt($this->curl_ptr, CURLOPT_NOPROGRESS, true); }
				curl_setopt($this->curl_ptr, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($this->curl_ptr, CURLOPT_TIMEOUT, $this->timeout_data);
				if (defined("CURLOPT_FILETIME")) { curl_setopt($this->curl_ptr, CURLOPT_FILETIME, false); }
				if (defined("CURLOPT_FOLLOWLOCATION")) { curl_setopt($this->curl_ptr, CURLOPT_FOLLOWLOCATION, true); }
				if (defined("CURLOPT_MAXREDIRS")) { curl_setopt($this->curl_ptr, CURLOPT_MAXREDIRS, 5); }
				if (defined("CURLOPT_UNRESTRICTED_AUTH")) { curl_setopt($this->curl_ptr, CURLOPT_UNRESTRICTED_AUTH, false); }
				if (defined("CURLOPT_USERAGENT")) { curl_setopt($this->curl_ptr, CURLOPT_USERAGENT, "directHttpCurl/#echo(phpRfcBasicsUAVersion)#"); }

				$headers = (isset($this->content_type) ? array("Content-Type: ".$this->content_type) : array());

				if (defined("CURLOPT_FORBID_REUSE") && defined("CURLOPT_URL"))
				{
					curl_setopt($this->curl_ptr, CURLOPT_FORBID_REUSE, false);
					$curl_close = false;
					$headers[] = "Connection: keep-alive";
					$headers[] = "Keep-Alive: ".$this->timeout_connection;
				}

				if (isset($data) && defined("CURLOPT_POSTFIELDS"))
				{
					$headers[] = "Content-Length: ".strlen($data);
					curl_setopt($this->curl_ptr, CURLOPT_POSTFIELDS, $data);
				}

				if ($curl_close) { $headers[] = "Connection: close"; }
				curl_setopt($this->curl_ptr, CURLOPT_HTTPHEADER, $headers);

				$response = curl_exec($this->curl_ptr);
				$error = curl_error($this->curl_ptr);

				if ($curl_close)
				{
					curl_close($this->curl_ptr);
					$this->curl_ptr = NULL;
				}

				if (strlen($error))
				{
					$this->data = "";
					$this->data_http_headers = array();
					$this->data_http_result_code = "error::".$error;
					if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->curlRequest()- received error: ".$error); }
				}
				else { $return = $this->responseParse($response); }
			}
			else
			{
				$this->data = "";
				$this->data_http_headers = array();
				$this->data_http_result_code = "error::invalid request";
				if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->curlRequest()- got an invalid request"); }
			}
		}
		else
		{
			$this->data = "";
			$this->data_http_headers = array();
			$this->data_http_result_code = "error::invalid request";
			if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->curlRequest()- got an invalid request"); }
		}

		return $return;
	}

/**
	* Sets a custom Content-Type header for requests. Use NULL to reset it.
	*
	* @param  mixed $type Mime-Type to be used or NULL for reset
	* @since  v0.1.00
*/
	public function defineContentType($type = NULL)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->defineContentType(+type)- (#echo(__LINE__)#)"); }
		$this->content_type = $type;
	}

/**
	* This operation just gives back the content of $this->data.
	*
	* @return mixed Returns the saved data
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function get()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->get()- (#echo(__LINE__)#)"); }
		return (isset($this->data) ? $this->data : false);
	}

/**
	* "getContent" is a wrapper for "get".
	*
	* @return string HTTP content
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getContent()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->getContent()- (#echo(__LINE__)#)"); }
		return $this->get();
	}

/**
	* Returns the size of the HTTP content.
	*
	* @return string HTTP content length value; false if undefined
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getContentSize()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->getContentSize()- (#echo(__LINE__)#)"); }
		$return = strlen($this->data);

		if (!$return)
		{
			$return = false;
			if (isset($this->data_http_headers['content-length'])) { $return = preg_replace("#(\D+)#", "", $this->data_http_headers['content-length']); }
		}

		return $return;
	}

/**
	* Returns the type of the HTTP content (given by the web server).
	*
	* @return string HTTP content type value; false if undefined
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getContentType()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->getContentType()- (#echo(__LINE__)#)"); }

		if ($this->data_http_headers && isset($this->data_http_headers['content-type'])) { $return = trim(preg_replace("#^(.+?);(.*?)$#s", "\\1", $this->data_http_headers['content-type'])); }
		else { $return = false; }

		return $return;
	}

/**
	* Returns the HTTP headers of the last call.
	*
	* @return array Found HTTP headers
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getHeaders()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->getHeaders()- (#echo(__LINE__)#)"); }
		return $this->data_http_headers;
	}

/**
	* Returns the HTTP result code of the last call.
	*
	* @return mixed Returned HTTP result code ("200") or error string
	*         "error:403:Forbidden"
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getResultCode()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->getResultCode()- (#echo(__LINE__)#)"); }
		return $this->data_http_result_code;
	}

/**
	* Returns the maximum time the process is waiting for all data to arrive.
	*
	* @return integer Time in seconds
	* @since  v0.1.03
*/
	/*#ifndef(PHP4) */public /* #*/function getTimeout()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->getTimeout()- (#echo(__LINE__)#)"); }
		return $this->timeout_data;
	}

/**
	* Returns the maximum time to wait for establishing a connection.
	*
	* @return integer Time in seconds
	* @since  v0.1.03
*/
	/*#ifndef(PHP4) */public /* #*/function getTimeoutConnection()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->getTimeoutConnection()- (#echo(__LINE__)#)"); }
		return $this->timeout_connection;
	}

/**
	* Converts an array of key => value pairs into an URL-encoded string.
	*
	* @param  array $data Input array
	* @param  boolean $form_data True to use multipart/form-data as data
	*         transfer mechanism
	* @return string URL-encoded string of $data
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function queryParse($data, $form_data = false)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->queryParse(+data, +form_data)- (#echo(__LINE__)#)"); }
		$return = "";

		if (is_string($data))
		{
			$data = str_replace(array("\r", "\n"), "", $data);
			if (!empty($data)) { parse_str($data, $data); }
		}

		if (is_array($data) && !empty($data))
		{
			foreach ($data as $key => $value)
			{
				if ($form_data)
				{
					$is_valid = true;

					if (is_array($value))
					{
						if (isset($value['value']))
						{
							if (isset($value['type']) && $value['type'] == "file")
							{
								if ($return) { $return .= "\r\n"; }

								$multipart = array("mimetype" => $value['mimetype'], "disposition" => "form-data", "data" => $value['value']);
								if (isset($value['encoding'])) { $multipart['encoding'] = $value['encoding']; }

								$is_valid = false;
								$return .= $this->multipartBody(rawurlencode($key), $multipart);
							}
							else { $value = $value['value']; }
						}
						else { $value = implode("\n", $value); }
/* -------------------------------------------------------------------------
The behaviour above might change for images in the future.
(multipart/alternative)
------------------------------------------------------------------------- */

					}

					if ($is_valid && trim($key) && trim($value))
					{
						if ($return) { $return .= "\r\n"; }

						$multipart = array("mimetype" => "text/plain", "disposition" => "form-data", "data" => $value);
						if ($this->data_encoding !== NULL) { $multipart['encoding'] = $this->data_encoding; }
						$return .= $this->multipartBody(rawurlencode($key), $multipart);
					}
				}
				else
				{
					if (is_array($value)) { $value = (((isset($value['value']) && isset($value['type']) && $value['type'] != "file") || (!isset($value['type']))) ? $value['value'] : implode("\n", $value)); }

					if (trim($key) && trim($value))
					{
						if ($return != "") { $return .= "&"; }
						$return .= rawurlencode($key)."=".rawurlencode($value);
					}
				}
			}
		}

		return $return;
	}

/**
	* This function provides a unified interface for the custom socket and the CURL
	* methods for getting HTTP content.
	*
	* @param  string $request Request type
	* @param  string $server Server name or IP address of target
	* @param  integer $port The target port
	* @param  string $path The absolute address to the remote resource
	* @param  string $query Query string
	* @param  string $data Body
	* @param  boolean $header_only Only receive the header
	* @param  integer $byte_first First byte to be read
	* @param  mixed $byte_last Last byte to be read (empty for EOF)
	* @return mixed Remote content on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function request($request, $server, $port = 80, $path, $query = "", $data = NULL, $header_only = false, $byte_first = "", $byte_last = "")
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->request($request, $server, $port, $path, +query, +data, +header_only, $byte_first, $byte_last)- (#echo(__LINE__)#)"); }
		$return = false;

		if ($query) { $query = "?".$query; }
		$range = NULL;

		if (is_int($byte_first))
		{
			if (is_int($byte_last))
			{
				if ($byte_first >= 0 && $byte_first < $byte_last) { $range = $byte_first."-".$byte_last; }
				elseif ($this->event_handler !== NULL)
				{
					$byte_last = "";
					$this->event_handler->warn("#echo(__FILEPATH__)# -webFunctions->request()- ignored an invalid HTTP range");
				}
			}
			elseif ($byte_first >= 0 && empty($byte_last)) { $range = $byte_first."-"; }
			elseif ($this->event_handler !== NULL)
			{
				$byte_first = "";
				$this->event_handler->warn("#echo(__FILEPATH__)# -webFunctions->request()- ignored an invalid HTTP range");
			}
		}

		if ($this->PHP_curl_init) { $return = $this->curlRequest($request, $server, $port, $path.$query, $data, $header_only, $byte_first, $byte_last); }
		elseif (USE_socket) { $return = $this->socketRequest($request, $server, $port, $path.$query, $data, $header_only, $byte_first, $byte_last); }
		elseif (@get_cfg_var("allow_url_fopen"))
		{
			if (preg_match("#^http(s|):\/\/#i", $server))
			{
				$file_context = array("method" => $request, "user_agent" => "directHttp/#echo(phpRfcBasicsUAVersion)#", "timeout" => $this->timeout_connection, "ignore_errors" => true, "max_redirects" => 5);

				$file_context['header'] = "Accept: */*\r\nAccept-Encoding: \r\nConnection: close";
				if (isset($range)) { $file_context['header'] .= "\r\nRange: bytes=".$range; }
				if (isset($this->content_type)) { $file_context['header'] .= "\r\nContent-Type: ".$this->content_type; }

				if (isset($data))
				{
					$file_context['content'] = $data;
					$file_context['header'] .= "\r\nContent-Length: ".strlen($data);
				}

				$file_context = array("http" => $file_context);
				$file_ptr = fopen($server.":".$port.$path.$query, "r", $file_context);

				if ($file_ptr)
				{
					@stream_set_timeout($file_ptr, $this->timeout_data);
					$response = "";

					if ($header_only)
					{
						if (!feof($file_ptr)) { $return = $this->responseParse("", false); }
						else
						{
							$this->data = "";
							$this->data_http_headers = array();
							$this->data_http_result_code = "error::timeout";
							if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->request()- timed out"); }
						}
					}
					else
					{
						$streams_read = array($file_ptr);
						$streams_ignored = NULL;
						$timeout_time = (time() + $this->timeout_data);

						while (!feof($file_ptr) && time() < $timeout_time)
						{
							if ($this->PHP_stream_select) { stream_select($streams_read, $streams_ignored, $streams_ignored, $this->timeout_data); }
							$response .= fread($file_ptr, 4096);
						}

						if (feof($file_ptr)) { $return = $this->responseParse($response, false); }
						else
						{
							$this->data = "";
							$this->data_http_headers = array();
							$this->data_http_result_code = "error::timeout";
							if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->request()- timed out"); }
						}
					}

					fclose($file_ptr);
				}
				else
				{
					$this->data = "";
					$this->data_http_headers = array();
					$this->data_http_result_code = "error::no response";
					if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->request()- received no response"); }
				}
			}
			else
			{
				$this->data = "";
				$this->data_http_headers = array();
				$this->data_http_result_code = "error::invalid request";
				if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->request()- got an invalid request"); }
			}
		}
		elseif ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->request()- has no possibility for opening remote content"); }

		return $return;
	}

/**
	* This function provides a unified interface for the socket, CURL and fopen
	* GET method for getting HTTP content.
	*
	* @param  string $server Server name or IP address of target
	* @param  integer $port The target port
	* @param  string $path The absolute address to the remote resource
	* @param  mixed $query Variables that should be transfered to the remote
	*         server (empty string for none)
	* @param  boolean $header_only Only receive the header
	* @return mixed Remote content on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function requestGet($server, $port = 80, $path = "", $query = "", $header_only = false)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->requestGet($server, $port, $path, +query, +header_only)- (#echo(__LINE__)#)"); }
		$return = false;

		$query = $this->queryParse($query);
		if ($query) { $query = "?".$query; }

		$this->data = "";
		$this->data_http_headers = array();
		$this->data_http_result_code = "";

		if ($this->PHP_curl_init) { $return = $this->curlRequest("GET", $server, $port, $path.$query, NULL, $header_only); }
		elseif (USE_socket) { $return = $this->socketRequest("GET", $server, $port, $path.$query, NULL, $header_only); }
		else { $return = $this->request("GET", $server, $port, $path.$query, NULL, $header_only); }

		return $return;
	}

/**
	* This function provides a unified interface for the socket and the CURL
	* POST method for getting HTTP content.
	*
	* @param  string $server Server name or IP address of target
	* @param  integer $port The target port
	* @param  string $path The absolute address to the remote resource
	* @param  mixed $data Variables that should be transfered to the remote
	*         server (empty string for none)
	* @param  boolean $parse Only receive the header
	* @return mixed Remote content on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function requestPost($server, $port = 80, $path = "", $data = "", $parse = true)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->requestPost($server, $port, $path, +data, +parse)- (#echo(__LINE__)#)"); }
		$return = false;

		$reset_content_type = true;
		$this->data = "";
		$this->data_http_headers = array();
		$this->data_http_result_code = "";

		if (is_string($data) && !$parse)
		{
			if (isset($this->content_type)) { $reset_content_type = false; }
			else { $this->defineContentType("application/x-www-form-urlencoded"); }
		}
		else
		{
			$this->defineContentType(substr($this->multipartHeader("multipart/form-data"), 14));

			$data = $this->queryParse($data, true);
			$data .= "\r\n".$this->multipartFooter();
		}

		if ($this->PHP_curl_init) { $return = $this->curlRequest("POST", $server, $port, $path, $data); }
		elseif (USE_socket) { $return = $this->socketRequest("POST", $server, $port, $path, $data); }
		else { $return = $this->request("POST", $server, $port, $path, $data); }

		if ($reset_content_type) { $this->defineContentType(NULL); }

		return $return;
	}

/**
	* This function provides a unified interface for the socket and the CURL
	* GET method to request a data range via HTTP.
	*
	* @param  string $server Server name or IP address of target
	* @param  integer $port The target port
	* @param  string $path The absolute address to the remote resource
	* @param  mixed $data Variables that should be transfered to the remote
	*         server (empty string for none)
	* @param  integer $byte_first First byte to be read
	* @param  mixed $byte_last Last byte to be read (empty for EOF)
	* @return mixed Remote content on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function requestRange($server, $port = 80, $path = "", $data = "", $byte_first = 0, $byte_last = "")
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->requestRange($server, $port, $path, +data, $byte_first, $byte_last)- (#echo(__LINE__)#)"); }
		$return = false;

		$query = $this->queryParse($data);
		if ($query) { $query = "?".$query; }

		$this->data = "";
		$this->data_http_headers = array();
		$this->data_http_result_code = "";

		if ($this->PHP_curl_init) { $return = $this->curlRequest("GET", $server, $port, $path.$query, $data, false, $byte_first, $byte_last); }
		elseif (USE_socket) { $return = $this->socketRequest("GET", $server, $port, $path.$query, $data, false, $byte_first, $byte_last); }
		else { $return = $this->request("GET", $server, $port, $path.$query, $data, false, $byte_first, $byte_last); }

		return $return;
	}

/**
	* Parses responses to filter headers.
	*
	* @param  string $data Remote response
	* @param  boolean $headers_supported False if headers are not given in
	*         $data.
	* @return string Content given by remote resource
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function responseParse($data, $headers_supported = true)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->responseParse(+data, +headers_supported)- (#echo(__LINE__)#)"); }

		if ($headers_supported)
		{
			$has_content = true;
			$is_valid = true;
			$data_array = explode("\r\n\r\n", $data, 2);

			$data_array[0] = $this->headerParse($data_array[0]);
			if (is_array($data_array[0])) { $this->data_http_headers = array_merge($this->data_http_headers, $data_array[0]); }

			if (isset($this->data_http_headers['transfer-encoding']) && /*#ifndef(PHP4) */stripos/* #*//*#ifdef(PHP4):stristr:#*/($this->data_http_headers['transfer-encoding'], "chunked") === 0)
			{
				$this->data = "";
				$data = $data_array[1];

				while ($is_valid && $has_content && strlen($data) > 0)
				{
					if (preg_match("#^(\w+)(|(;| ;)(.+?))\r\n#i", $data, $result_array))
					{
						$data = substr($data,(strlen($result_array[0])));
						$size_octet = hexdec($result_array[1]);

						if ($size_octet == 0 && isset($this->data_http_headers['trailer']))
						{
							$headers = $this->headerParse($data);
							$has_content = false;

							if (is_array($headers)) { $this->data_http_headers = array_merge($this->data_http_headers, $headers); }
						}
						elseif (strlen($data) > $size_octet)
						{
							$this->data .= substr($data, 0, $size_octet);
							$data = substr($data, ($size_octet + 2));
						}
						else { $is_valid = false; }
					}
					elseif (!preg_match("#^0(|(;| ;)(.+?))\r\n#i", $data))
					{
						$this->data .= $data;
						$has_content = false;
					}
					else { $is_valid = false; }
				}
			}
			else { $this->data = $data_array[1]; }

			if ($is_valid) { $this->data_http_result_code = (preg_match("#^HTTP/(\d).(\d) (\d{1,3})(.*?)$#im", $this->data_http_headers['@untagged'], $result_array) ? $result_array[3] : "error::malformed response"); }
			else
			{
				$this->data = "";
				$this->data_http_result_code = "error::malformed response";
			}
		}
		else
		{
			$this->data = $data;
			$this->data_http_headers = array("x-error" => "unsupported");
			$this->data_http_result_code = 200;
		}

		return $this->data;
	}

/**
	* This operation fills $this->data with $data.
	*
	* @param mixed $data Data to be saved
	* @since v0.1.03
*/
	/*#ifndef(PHP4) */public /* #*/function set($data)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->set(+data)- (#echo(__LINE__)#)"); }
		$this->data = $data;
	}

/**
	* Sets the maximum time the process is waiting for all data to arrive.
	*
	* @param integer $timeout Time in seconds
	* @since v0.1.03
*/
	/*#ifndef(PHP4) */public /* #*/function setTimeout($timeout)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->setTimeout($timeout)- (#echo(__LINE__)#)"); }
		$this->timeout_data = $timeout;
	}

/**
	* Defines the maximum time to wait for establishing a connection.
	*
	* @param integer $timeout Time in seconds
	* @since v0.1.03
*/
	/*#ifndef(PHP4) */public /* #*/function setTimeoutConnection($timeout)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->setTimeoutConnection($timeout)- (#echo(__LINE__)#)"); }
		$this->timeout_connection = $timeout;
	}

/**
	* Does an HTTP request using fsockopen.
	*
	* @param  string $request Request type
	* @param  string $server Server name or IP address of target
	* @param  integer $port The target port
	* @param  string $query Query string
	* @param  mixed $data Variables that should be transfered to the remote
	*         server (empty string for none)
	* @param  boolean $header_only Only receive the header
	* @return mixed Remote content on success; false on error
	* @param  integer $byte_first First byte to be read
	* @param  mixed $byte_last Last byte to be read (empty for EOF)
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function socketRequest($request, $server, $port = 80, $query = "", $data = NULL, $header_only = false, $byte_first = "", $byte_last = "")
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -webFunctions->socketRequest($request, $server, $port, $query, +data, +header_only, $byte_first, $byte_last)- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_int($byte_first)) { $range = (is_int($byte_last) ? $byte_first."-".$byte_last : $byte_first."-"); }
		else { $range = NULL; }

		if (preg_match("#^http(s|):\/\/(.+?)$#i", $server, $result_array))
		{
			$error = "";
			$error_code = 0;
			$stream_ptr = @fsockopen($result_array[2], $port, $error_code, $error, $this->timeout_connection);

			if ($error_code || $error)
			{
				$this->data = "";
				$this->data_http_headers = array();
				$this->data_http_result_code = "error:$error_code:".$error;
				if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->socketRequest()- received error: $error ($error_code)"); }
			}
			elseif ($stream_ptr)
			{
				@stream_set_blocking($stream_ptr, 0);
				@stream_set_timeout($stream_ptr, $this->timeout_data);

				$request = $request." $query HTTP/1.1\r\nHost: {$result_array[2]}\r\nUser-Agent: directHttpSocket/#echo(phpRfcBasicsUAVersion)#\r\nAccept: */*\r\nAccept-Encoding: \r\nConnection: close\r\n";
				if (isset($range)) { $request .= "Range: bytes=$range\r\n"; }
				if (isset($this->content_type)) { $request .= "Content-Type: ".$this->content_type."\r\n"; }
				$request .= (isset($data) ? "Content-Length: ".strlen($data)."\r\n\r\n".$data : "\r\n");

				fwrite($stream_ptr, $request);

				if (!@feof($stream_ptr))
				{
					$response = "";
					$streams_read = array($stream_ptr);
					$streams_ignored = NULL;
					$timeout_time = (time() + $this->timeout_data);

					while (!feof($stream_ptr) && time() < $timeout_time)
					{
						if ($this->PHP_stream_select) { stream_select($streams_read, $streams_ignored, $streams_ignored, $this->timeout_data); }
						$response .= fread($stream_ptr, ($header_only ? 1024 : 4096));
					}

					if (feof($stream_ptr)) { $return = $this->responseParse($response); }
					else
					{
						$this->data = "";
						$this->data_http_headers = array();
						$this->data_http_result_code = "error::timeout";
						if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->socketRequest()- timed out"); }
					}
				}
				else
				{
					$this->data = "";
					$this->data_http_headers = array();
					$this->data_http_result_code = "error::no response";
					if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->socketRequest()- received no response"); }
				}

				fclose($stream_ptr);
			}
		}
		else
		{
			$this->data = "";
			$this->data_http_headers = array();
			$this->data_http_result_code = "error::invalid request";
			if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -webFunctions->socketRequest()- got an invalid request"); }
		}

		return $return;
	}
}

//j// EOF