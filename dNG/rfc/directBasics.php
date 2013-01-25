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
* This class provides basic functions described in different RFCs.
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
* This class should be extended to provide some RFC defined methods.
*
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   rfc_basics.php
* @since     v0.1.00
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
class directBasics
{
/**
	* @var string $data_boundary Boundary string for this message
*/
	protected $data_boundary = "";
/**
	* @var string $data_encoding Content encoding
*/
	protected $data_encoding = "UTF-8";
/**
	* @var string $data_unique Unique value for each instance
*/
	protected $data_unique;
/**
	* @var object $event_handler The EventHandler is called whenever debug messages
	*      should be logged or errors happened.
*/
	protected $event_handler;
/**
	* @var string $linesep Line separator (\r\n or \n)
*/
	protected $linesep = "\r\n";
/**
	* @var boolean $PHP_mb_encode_mimeheader True if the PHP function
	*      "mb_encode_mimeheader () " is supported.
*/
	protected $PHP_mb_encode_mimeheader;
/*#ifndef(PHP5n):
/**
	* @var boolean $PHP_quoted_printable_encode True if the PHP function
	*      "quoted_printable_encode () " is supported.
*\/
	protected $PHP_quoted_printable_encode;
:#\n*/

/* -------------------------------------------------------------------------
Construct the class
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directBasics)
	*
	* @param object $event_handler EventHandler to use
	* @since v0.1.00
*/
	public function __construct($event_handler = NULL)
	{
		if ($event_handler !== NULL) { $event_handler->debug("#echo(__FILEPATH__)# -rfcBasics->__construct(directBasics)- (#echo(__LINE__)#)"); }

		$this->data_unique = uniqid();
		$this->event_handler = $event_handler;
		$this->PHP_mb_encode_mimeheader = function_exists("mb_encode_mimeheader");
/*#ifndef(PHP5n):
		$this->PHP_quoted_printable_encode = function_exists("quoted_printable_encode");
:#\n*/
	}
/**
	* Destructor (PHP5+) __destruct (directBasics)
	*
	* @since v0.1.00
*/
	public function __destruct() { /* Nothing to do for me */ }

/**
	* Sets a custom encoding.
	*
	* @param  mixed $encoding Encoding to be used
	* @since  v0.1.00
*/
	public function defineEncoding($encoding)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -rfcBasics->defineEncoding($encoding)- (#echo(__LINE__)#)"); }
		$this->data_encoding = $encoding;
	}

/**
	* Aligns the header to the line size limit using folding.
	*
	* @param  string $header Input header string
	* @return string Aligned header (maximum 76 characters)
	* @since  v0.1.00
*/
	protected function headerAlign($header)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -rfcBasics->headerAlign(+header)- (#echo(__LINE__)#)"); }

		if (strlen($header) > 76)
		{
			$length = 0;
			$words = explode(" ", $header);
			$return = "";

			foreach ($words as $word)
			{
				$length += strlen($word);

				if ($length > 76)
				{
					$return .= $this->linesep." ";
					$length = strlen($word) + 1;
				}
				elseif ($return)
				{
					$length++;
					$return .= " ";
				}

				$return .= $word;
			}
		}
		else { $return = $header; }

		return $return;
	}

/**
	* Parses a string of headers.
	*
	* @param  string $headers Input header string
	* @return mixed Array with parsed headers; false on error
	* @since  v0.1.00
*/
	public function headerParse($headers)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -rfcBasics->headerParse(+headers)- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_string($headers) && strlen($headers))
		{
			$headers = trim(preg_replace("#\r\n((\\x09)(\\x09)*|(\\x20)(\\x20)*)(\S)#", "\\2\\4\\6", $headers));
			$return = array();

			$headers = explode("\r\n", $headers);

			foreach ($headers as $header)
			{
				$header = explode(":", $header, 2);

				if (count($header) == 2)
				{
					$header_name = strtolower($header[0]);
					$header[1] = trim($header[1]);

					if (isset($return[$header_name]))
					{
						if (is_array($return[$header_name])) { $return[$header_name][] = $header[1]; }
						else { $return[$header_name] = array($return[$header_name], $header[1]); }
					}
					else { $return[$header_name] = $header[1]; }
				}
				elseif (strlen($header[0]))
				{
					if (isset($return['@untagged'])) { $return['@untagged'] .= "\n".trim($header[0]); }
					else { $return['@untagged'] = trim($header[0]); }
				}
			}
		}
		elseif ($this->event_handler !== NULL) { $this->event_handler->warning("#echo(__FILEPATH__)# -rfcBasics->headerParse()- got invalid input to parse"); }

		return $return;
	}

/**
	* Returns a multipart body header.
	*
	* @param  string $name Content file name if applicable
	* @param  array $data Attachement data array
	* @param  string $alternative_id Multipart ID for alternative body data
	* @return string Multipart body header
	* @since  v0.1.00
*/
	protected function multipartBody($name, $data, $alternative_id = "")
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -rfcBasics->multipartBody($name, +data, $alternative_id)- (#echo(__LINE__)#)"); }
		$return = (strlen($alternative_id) ? "--=".$alternative_id : "--").$this->data_boundary.$this->linesep;

		if (strpos($name, "@") === false)
		{
			$name = preg_replace("#[;\/\\\?:@\=\"\&\']#", "_", $name);

			$return .= $this->headerAlign("Content-Type: ".$data['mimetype']."; name=\"$name\"".($data['encoding'] ? "; charset=".$data['encoding'] : "")).$this->linesep;
			$disposition = (isset($data['disposition']) ? $data['disposition'] : "attachment");

			if (strlen($name)) { $disposition_fields = (($disposition == "attachment") ? "; filename=\"$name\"" : "; name=\"$name\""); }
			else { $disposition_fields = ""; }

			if (stripos($data['mimetype'], "text/") === false)
			{
				$return .= $this->headerAlign("Content-Transfer-Encoding: base64").$this->linesep;
				$return .= $this->headerAlign("Content-Disposition: ".$disposition.$disposition_fields).$this->linesep.$this->linesep;
				$return .= wordwrap(base64_encode($data['data']), 76, $this->linesep, 1);
			}
			else
			{
				$return .= $this->headerAlign("Content-Transfer-Encoding: quoted-printable").$this->linesep;
				$return .= $this->headerAlign("Content-Disposition: ".$disposition.$disposition_fields).$this->linesep.$this->linesep;
				$return .= $this->quotedPrintableEncode($data['data']);
			}
		}
		else
		{
			$return .= $this->headerAlign("Content-Type: ".$data['mimetype']."; charset=".$data['encoding']).$this->linesep;
			$return .= $this->headerAlign("Content-Transfer-Encoding: quoted-printable").$this->linesep.$this->linesep;
			$return .= $this->quotedPrintableEncode($data['data']);
		}

		return $return;
	}

/**
	* Returns a multipart body alternative footer.
	*
	* @param  string $id Multipart alternative ID
	* @return string Multipart alternative footer
	* @since  v0.1.00
*/
	protected function multipartBodyAlternativeFooter($id) { return "--=".$id.$this->data_boundary."--"; }

/**
	* Returns a multipart body alternative header.
	*
	* @param  string $id Multipart alternative ID
	* @return string Multipart alternative header
	* @since  v0.1.00
*/
	protected function multipartBodyAlternativeHeader($id) { return "--".$this->data_boundary.$this->linesep.($this->headerAlign("Content-Type: multipart/alternative; boundary=\"=".$id.$this->data_boundary."\"")); }

/**
	* Returns a multipart body footer.
	*
	* @return string Multipart footer
	* @since  v0.1.00
*/
	protected function multipartFooter() { return "--".$this->data_boundary."--"; }

/**
	* Adds the unique multipart header to the message.
	*
	* @param  string Content type of this multipart element
	* @return string Multipart header declaration (including boundary)
	* @since  v0.1.00
*/
	protected function multipartHeader($header_content_type = "multipart/related")
	{
		$this->data_boundary = uniqid("=_direct-mime_".$this->data_unique);
		return $this->headerAlign("Content-Type: $header_content_type; boundary=\"".$this->data_boundary."\"");
	}

/**
	* Formats a given string and returns a valid Quoted Printable one.
	*
	* @param  string $data Input string
	* @param  boolean $rfc2047 Encode data according to RFC 2047
	* @return string Formatted output string; empty on error
	* @since  v0.1.00
*/
	protected function quotedPrintableEncode($data, $rfc2047 = false)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -rfcBasics->quotedPrintableEncode(+data, +rfc2047)- (#echo(__LINE__)#)"); }

		$has_cr = false;
		$has_lf = true;
		$words = NULL;

		if ($rfc2047)
		{
			if ($this->PHP_mb_encode_mimeheader)
			{
				$return = mb_encode_mimeheader($data);
				if ($this->linesep != "\r\n") { $return = str_replace("\r\n", $this->linesep, $return); }
			}
			else
			{
				$return = "=?{$this->data_encoding}?Q?";
				$rfc2047_length = strlen($return);

				$length = $rfc2047_length;
				$words = explode(" ", $data);
			}
		}
		/*#ifdef(PHP5n) */else/* #*//*#ifndef(PHP5n):elseif ($this->PHP_quoted_printable_encode):#*/
		{
			$return = quoted_printable_encode($data);
			if ($this->linesep != "\r\n") { $return = str_replace("\r\n", $this->linesep, $return); }
		}
/*#ifndef(PHP5n):
		else
		{
			$length = 0;
			$words = explode(" ", $data);
		}
:#*/
		if (isset($words))
		{
			foreach ($words as $word)
			{
				if (strlen($word))
				{
					$word_filtered = "";
					$word_length = strlen($word);

					for ($char_number = 0;$char_number < $word_length;$char_number++)
					{
						$char = ord($word[$char_number]);

						if ((32 < $char && $char < 61) || (61 < $char && $char < 127))
						{
							if ($has_cr) { $has_cr = false; }
							if ($has_lf) { $has_lf = false; }
							$length++;
							$word_filtered .= $word[$char_number];
						}
						else
						{
							switch ($char)
							{
							case 9:
							{
								if ($has_lf)
								{
									$length += 3;
									$word_filtered .= "=09";
								}
								else
								{
									$length++;
									$word_filtered .= $word[$char_number];
								}

								break 1;
							}
							case 10:
							{
								if (!$has_cr)
								{
									if ($rfc2047)
									{
										$length = 1 + $rfc2047_length;
										$word_filtered .= "?={$this->linesep} =?{$this->data_encoding}?Q?";
									}
									else
									{
										$length = strlen($this->linesep);
										$word_filtered .= $this->linesep;
									}
								}

								$has_cr = false;
								$has_lf = true;
								break 1;
							}
							case 13:
							{
								$has_cr = true;
								$has_lf = true;

								if ($rfc2047)
								{
									$length = 1 + $rfc2047_length;
									$word_filtered .= "?={$this->linesep} =?{$this->data_encoding}?Q?";
								}
								else
								{
									$length = strlen($this->linesep);
									$word_filtered .= $this->linesep;
								}

								break 1;
							}
							default:
							{
								if ($has_cr) { $has_cr = false; }
								if ($has_lf) { $has_lf = false; }
								$length += 3;
								$word_filtered .= "=".strtoupper(dechex($char));
							}
							}
						}

						if ($length > 72)
						{
							if (strlen($word_filtered) < 73)
							{
								if ($rfc2047)
								{
									$length = strlen($word_filtered) + 1 + $rfc2047_length;
									$word_filtered = "?={$this->linesep} =?{$this->data_encoding}?Q?".$word_filtered;
								}
								else
								{
									$length = strlen($word_filtered);
									$word_filtered = "=".$this->linesep.$word_filtered;
								}
							}
							else
							{
								$has_cr = true;
								$has_lf = true;

								if ($rfc2047)
								{
									$length = 1 + $rfc2047_length;
									$word_filtered .= "?={$this->linesep} =?{$this->data_encoding}?Q?";
								}
								else
								{
									$length = 0;
									$word_filtered .= "=".$this->linesep;
								}
							}
						}
					}

					if ($has_lf || $rfc2047)
					{
						$return .= $word_filtered."=20";
						$length += 3;
					}
					else
					{
						$return .= $word_filtered." ";
						$length++;
					}
				}
				else
				{
					if ($length > 72)
					{
						$has_lf = true;

						if ($rfc2047)
						{
							$length = 1 + $rfc2047_length;
							$return .= "?={$this->linesep} =?{$this->data_encoding}?Q?";
						}
						else
						{
							$length = 0;
							$return .= "=".$this->linesep;
						}
					}

					if ($has_lf || $rfc2047)
					{
						$length += 3;
						$return .= "=20";
					}
					else
					{
						$length++;
						$return .= " ";
					}
				}
			}

			$return = ($rfc2047 ? preg_replace("#(=20)*\?=({$this->linesep} =\?".preg_quote($this->data_encoding, "#")."\?Q\?(=20)+\?=)*$#s", "?=", $return."?=") : rtrim($return));
		}

		return $return;
	}

/**
	* Sets the EventHandler.
	*
	* @param object $event_handler EventHandler to use
	* @since v0.1.00
*/
	public function setEventHandler($event_handler)
	{
		if ($event_handler !== NULL) { $event_handler->debug("#echo(__FILEPATH__)# -rfcBasics->setEventHandler(+event_handler)- (#echo(__LINE__)#)"); }
		$this->event_handler = $event_handler;
	}
}

//j// EOF