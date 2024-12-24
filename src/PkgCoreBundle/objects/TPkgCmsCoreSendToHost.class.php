<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TPkgCmsCoreSendToHost
 * use the class to send http requests to a remote server.
 */
class TPkgCmsCoreSendToHost
{
    public const METHOD_GET = 'GET';
    public const METHOD_HEAD = 'HEAD';
    public const METHOD_POST = 'POST';

    /**
     * @var array<string, mixed>
     */
    private $aConfig = [];

    /**
     * @var string|null
     */
    private $lastRequest;

    /**
     * @var string|null
     */
    private $lastResponseHeader;

    /**
     * @var array<string, string|non-empty-list<string>>
     */
    private $lastResponseHeaderVariables;

    /**
     * @var int|null
     */
    private $lastResponseCode;

    /**
     * @var string|null
     */
    private $lastResponseCodeRaw;

    /**
     * @var string|null
     */
    private $lastResponseBody;

    /**
     * @var bool
     */
    private $logRequest = false;

    /**
     * @param string|null $sUrl : http[s]://www.my.tld/path?q1=v1&q2=v2...
     */
    public function __construct($sUrl = null)
    {
        if (null !== $sUrl) {
            $this->setConfigFromUrl($sUrl);
        }
    }

    /**
     * @param string $sUrl http[s]://www.my.tld/path?q1=v1&q2=v2...
     *
     * @return $this
     */
    public function setConfigFromUrl($sUrl)
    {
        $this->setConfigVar('url', $sUrl);

        $aUrlDetails = parse_url($sUrl);
        $bUseSSL = 'https' === $aUrlDetails['scheme'];
        $this->setUseSSL($bUseSSL);

        if (isset($aUrlDetails['host'])) {
            $this->setHost($aUrlDetails['host']);
        }
        if (isset($aUrlDetails['path'])) {
            $this->setPath($aUrlDetails['path']);
        }
        if (isset($aUrlDetails['user'])) {
            $this->setUser($aUrlDetails['user']);
        }
        if (isset($aUrlDetails['pass'])) {
            $this->setPassword($aUrlDetails['pass']);
        }
        if (isset($aUrlDetails['query'])) {
            $aData = null;
            parse_str($aUrlDetails['query'], $aData);
            $this->setPayload($aData);
        }

        return $this;
    }

    /**
     * @param string $sHeader
     *
     * @return void
     */
    private function parseResponseHeader($sHeader)
    {
        $sHeader = trim($sHeader);
        if ('' === $sHeader) {
            return;
        }

        $rHeader = fopen('data://text/plain,'.$sHeader, 'r');
        if (!$rHeader) {
            return;
        }

        $this->lastResponseCodeRaw = fgets($rHeader);
        $aResponseCodeParts = explode(' ', $this->lastResponseCodeRaw);
        if (count($aResponseCodeParts) >= 2) {
            $this->lastResponseCode = intval(trim($aResponseCodeParts[1]));
        }

        $this->lastResponseHeaderVariables = [];

        while (false !== ($buffer = fgets($rHeader))) {
            $sLine = trim($buffer);
            $iSplit = strpos($sLine, ':');
            $sHeaderVarName = trim(substr($sLine, 0, $iSplit));
            $sHeaderVarValue = trim(substr($sLine, $iSplit));
            if (isset($this->lastResponseHeaderVariables[$sHeaderVarName])) {
                if (!is_array($this->lastResponseHeaderVariables[$sHeaderVarName])) {
                    $this->lastResponseHeaderVariables[$sHeaderVarName] = [$this->lastResponseHeaderVariables[$sHeaderVarName]];
                }
                $this->lastResponseHeaderVariables[$sHeaderVarName][] = $sHeaderVarValue;
            } else {
                $this->lastResponseHeaderVariables[$sHeaderVarName] = $sHeaderVarValue;
            }
        }
        fclose($rHeader);
    }

    /**
     * return a header variable.
     *
     * @param string $sVarName
     *
     * @return string|non-empty-list<string>|null
     */
    public function getLastResponseHeaderVariable($sVarName)
    {
        if (null === $this->lastResponseHeaderVariables) {
            $this->lastResponseHeaderVariables = [];

            /**
             * @psalm-suppress UndefinedClass
             *
             * @var resource $rHeader
             *
             * @see https://github.com/chameleon-system/chameleon-system/issues/772
             */
            $rHeader = TPkgCmsStringUtilities::getStringStream($this->getLastResponseHeader());

            $this->lastResponseCodeRaw = fgets($rHeader);

            $aTmp = explode("\n", $this->getLastResponseHeader());
            foreach ($aTmp as $sHeaderLine) {
                $iSplit = strpos($sHeaderLine, ':');
                $sHeaderVarName = trim(substr($sHeaderLine, 0, $iSplit));
                $sHeaderVarValue = trim(substr($sHeaderLine, $iSplit));
                if (isset($this->lastResponseHeaderVariables[$sHeaderVarName])) {
                    if (!is_array($this->lastResponseHeaderVariables[$sHeaderVarName])) {
                        $this->lastResponseHeaderVariables[$sHeaderVarName] = [$this->lastResponseHeaderVariables[$sHeaderVarName]];
                    }
                    $this->lastResponseHeaderVariables[$sHeaderVarName][] = $sHeaderVarValue;
                } else {
                    $this->lastResponseHeaderVariables[$sHeaderVarName] = $sHeaderVarValue;
                }
            }
        }

        if (isset($sVarName, $this->lastResponseHeaderVariables)) {
            return $this->lastResponseHeaderVariables[$sVarName];
        } else {
            return null;
        }
    }

    /**
     * @param string $key
     *
     * @return void
     */
    private function setConfigVar($key, $val)
    {
        $this->aConfig[$key] = $val;
    }

    /**
     * @param bool $bUseSSL
     *
     * @return static
     */
    public function setUseSSL($bUseSSL)
    {
        if (true !== $bUseSSL && false !== $bUseSSL) {
            throw new TPkgCmsException_Log(
                'setUseSSL called with invalid value',
                [
                     'context' => $this,
                     'param' => $bUseSSL,
                ], 1);
        }
        $this->setConfigVar('useSSL', $bUseSSL);

        return $this;
    }

    /**
     * @param string $sHost
     *
     * @return static
     */
    public function setHost($sHost)
    {
        $this->setConfigVar('host', $sHost);

        return $this;
    }

    /**
     * @param string $sPath
     *
     * @return static
     */
    public function setPath($sPath)
    {
        $this->setConfigVar('path', $sPath);

        return $this;
    }

    /**
     * @param string|null $sUser
     *
     * @return static
     */
    public function setUser($sUser)
    {
        $this->setConfigVar('user', $sUser);

        return $this;
    }

    /**
     * @param string|null $sPassword
     *
     * @return static
     */
    public function setPassword($sPassword)
    {
        $this->setConfigVar('password', $sPassword);

        return $this;
    }

    /**
     * @param array $aPayload
     *
     * @return static
     */
    public function setPayload($aPayload)
    {
        if (false === is_array($aPayload)) {
            throw new TPkgCmsException_Log(
                'setPayload called with invalid value',
                [
                     'context' => $this,
                     'param' => $aPayload,
                ], 1);
        }
        $this->setConfigVar('payload', $aPayload);

        return $this;
    }

    /**
     * @return string
     *
     * @throws TPkgCmsException_Log
     */
    public function executeRequest()
    {
        $this->lastRequest = null;
        $this->lastResponseHeader = null;
        $this->lastResponseBody = null;

        $aData = $this->getConfigVar('payload', []);
        $data = TTools::GetArrayAsURL($aData);
        $data = str_replace('&amp;', '&', $data);
        $path = $this->getConfigVar('path', '/');
        $host = $this->getConfigVar('host');
        $contentType = $this->getConfigVar('contentType');
        $sUser = $this->getConfigVar('user', null);
        $sPassword = $this->getConfigVar('password', null);

        // Supply a default method of GET if the one passed was empty
        $method = $this->getConfigVar('method', self::METHOD_GET);

        $buf = '';
        $sPort = 80;
        $sfSockURL = $this->getConfigVar('host');
        if (true === $this->getConfigVar('useSSL')) {
            $sPort = 443;
            $sfSockURL = 'ssl://'.$sfSockURL;
        }

        $sConnectionError = null;
        $sConnectionErrorMessage = null;
        if ($fp = fsockopen(
            $sfSockURL,
            $sPort,
            $sConnectionError,
            $sConnectionErrorMessage,
            $this->getConfigVar('timeout', 30)
        )
        ) {
            if ('' !== $data && (self::METHOD_GET === $method || self::METHOD_HEAD === $method)) {
                $path .= '?'.$data;
            }
            $sRequest = '';

            $sRequest .= "{$method} {$path} HTTP/1.1\r\n";
            $sRequest .= "Host: {$host}\r\n";
            $sRequest .= 'Content-Type: '.$contentType."\r\n";
            if (self::METHOD_POST === $method) {
                $sRequest .= 'Content-length: '.strlen($data)."\r\n";
            }
            if ($this->getConfigVar('sendUserAgent', false)) {
                $sRequest .= "User-Agent: MSIE\r\n";
            }
            if (null !== $sUser && null !== $sPassword) {
                $sAuth = base64_encode($sUser.':'.$sPassword);
                $sRequest .= "Authorization: Basic {$sAuth}\r\n";
            }

            $sRequest .= "Connection: close\r\n\r\n";

            if (self::METHOD_POST === $method) {
                $sRequest .= $data;
            }
            $this->lastRequest = $sRequest;
            fwrite($fp, $sRequest, strlen($sRequest));

            $buf = '';
            while (!feof($fp)) {
                $buf .= fgets($fp, 128);
            }

            fclose($fp);
        } else {
            throw new TPkgCmsException_Log("Error connecting to {$sfSockURL} on port {$sPort}",
                [
                     'context' => $this,
                     'errorNr' => $sConnectionError,
                     'errorMessage' => $sConnectionErrorMessage,
                ],
                1
            );
        }
        $sHeader = substr($buf, 0, strpos($buf, "\r\n\r\n") + 4);
        if (self::METHOD_HEAD === $method) {
            $sResponse = '';
        } else {
            $sResponse = substr($buf, strpos($buf, "\r\n\r\n") + 4);
            if (false !== strpos(strtolower($sHeader), 'transfer-encoding: chunked')) {
                $fp = 0;
                $outData = '';
                $data = $sResponse;
                while ($fp < strlen($data)) {
                    $rawnum = substr($data, $fp, strpos(substr($data, $fp), "\r\n") + 2);
                    $num = hexdec(trim($rawnum));
                    $fp += strlen($rawnum);
                    $chunk = substr($data, $fp, $num);
                    $outData .= $chunk;
                    $fp += strlen($chunk);
                }
                $sResponse = $outData;
            }
        }
        $this->lastResponseHeader = $sHeader;
        $this->lastResponseBody = $sResponse;

        $this->parseResponseHeader($sHeader);

        $msg = 'REQUEST: '.$this->getLastRequest().' RESPONSE: '.$this->getLastResponseHeader().$this->getLastResponseBody();
        $msg = str_replace(["\r", "\n"], ['', '\n'], $msg);
        if (Response::HTTP_OK === $this->getLastResponseCode()) {
            $this->getLogger()->info($msg);
        } else {
            $this->getLogger()->error($msg);
        }

        return $this->lastResponseBody;
    }

    /**
     * @param string $key
     */
    private function getConfigVar($key, $default = null)
    {
        if (true === isset($this->aConfig[$key])) {
            return $this->aConfig[$key];
        }

        return $default;
    }

    /**
     * @return string|null
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return string|null
     */
    public function getLastResponseHeader()
    {
        return $this->lastResponseHeader;
    }

    /**
     * @return string|null
     */
    public function getLastResponseBody()
    {
        return $this->lastResponseBody;
    }

    /**
     * make sure your query string does not contain &amp; for &.
     *
     * @param string $sQueryString (k1=v1&k2=v2)
     *
     * @return $this
     */
    public function setPayloadFromQueryString($sQueryString)
    {
        $aData = null;
        parse_str($sQueryString, $aData);

        return $this->setPayload($aData);
    }

    /**
     * @param string $sMethod (must be one of TPkgCmsCoreSendToHost::METHOD_*)
     *
     * @psalm-param self::METHOD_* $sMethod
     *
     * @return $this
     */
    public function setMethod($sMethod)
    {
        $sMethod = strtoupper($sMethod);
        switch ($sMethod) {
            case self::METHOD_GET:
            case self::METHOD_HEAD:
            case self::METHOD_POST:
                break;
            default:
                throw new TPkgCmsException_Log(
                    'setMethod called with invalid value',
                    [
                         'context' => $this,
                         'param' => $sMethod,
                    ], 1);
                break;
        }
        $this->setConfigVar('method', $sMethod);

        return $this;
    }

    /**
     * @param bool $bAgent
     *
     * @return $this
     */
    public function setSendUserAgent($bAgent)
    {
        if (true !== $bAgent && false !== $bAgent) {
            throw new TPkgCmsException_Log(
                'setSendUserAgent called with invalid value',
                [
                     'context' => $this,
                     'param' => $bAgent,
                ], 1);
        }
        $this->setConfigVar('sendUserAgent', $bAgent);

        return $this;
    }

    /**
     * @param string $sContentType
     *
     * @return $this
     */
    public function setContentType($sContentType)
    {
        $this->setConfigVar('contentType', $sContentType);

        return $this;
    }

    /**
     * @param int $iTimeout
     *
     * @return $this
     */
    public function setTimeout($iTimeout)
    {
        $iTimeout = (int) $iTimeout;

        $this->setConfigVar('timeout', $iTimeout);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLastResponseCode()
    {
        return $this->lastResponseCode;
    }

    /**
     * @return string|null
     */
    public function getLastResponseCodeRaw()
    {
        return $this->lastResponseCodeRaw;
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}
