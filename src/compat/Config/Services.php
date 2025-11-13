<?php

namespace Config;

/**
 * Minimal Services stub providing curlrequest() used by Client.
 */
class Services
{
    public static function curlrequest($options = [])
    {
        return new class {
            public function request($method, $url, $options)
            {
                return new class {
                    public function getStatusCode()
                    {
                        return 200;
                    }

                    public function getBody()
                    {
                        return json_encode(['ok' => true]);
                    }
                };
            }
        };
    }
}
