<?php
declare(strict_types=1);

namespace SectionBuilder\Core\Plugin\Ui\Component\Form;

class DataProvider
{
    protected $config;

    public function __construct(
        \SectionBuilder\Core\Model\Config $config
    ) {
        $this->config = $config;
    }

    public function afterGetData($subject, $result)
    {
        $appConnecting = $this->config->getAppConnectingId();
        foreach ($result as &$data) {
            if ($data['app_id'] == $appConnecting) {
                $data['is_section_builder_app'] = 1;
            } else {
                $data['is_section_builder_app'] = 0;
            }
        }

        return $result;
    }
}
