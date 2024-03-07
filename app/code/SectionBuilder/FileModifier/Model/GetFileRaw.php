<?php
declare(strict_types=1);

namespace SectionBuilder\FileModifier\Model;

class GetFileRaw
{
    const REPOSITORY_FILES_PATH = '/repository/files/';

    protected $configData;

    protected $curl;

    protected $logger;

    public function __construct(
        \SectionBuilder\Core\Model\Config $configData,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configData = $configData;
        $this->curl = $curl;
        $this->logger = $logger;
    }

    public function execute($path)
    {
        try {
            $privateToken = $this->configData->getApiToken();
            $ref = $this->configData->getApiRef();
            $url = $this->configData->getApiBaseUrl() . self::REPOSITORY_FILES_PATH . urlencode($path) . "/raw";

            $queryParams = [
                'ref' => urlencode($ref)
            ];
            $queryString = http_build_query($queryParams);
            if (!empty($queryString)) {
                $url .= '?' . $queryString;
            }

            $this->curl->addHeader('PRIVATE-TOKEN', $privateToken);
            $this->curl->get($url);

            $response = $this->curl->getBody();
            if ($this->curl->getStatus() === 200) {
                return $response;
            }

            $this->logger->error('Section Builder log: ' . $response);
        } catch (\Exception $e) {
            $this->logger->error('Section Builder log: ' . $e->getMessage());
        }

        return '';
    }
}
