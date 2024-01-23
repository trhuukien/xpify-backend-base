<?php
declare(strict_types=1);

namespace Xpify\ThemeGraphQl\Model;

class Validation
{
    /**
     * Validate input arguments
     *
     * @param array $args
     * @param array $validationRequire
     * @param array $validationEmpty
     * @return void
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlInputException
     */
    public function validateArgs($args, $validationRequire = [], $validationEmpty = [])
    {
        foreach ($validationRequire as $val) {
            if (!isset($args[$val]) || $args[$val] == '') {
                throw new \Magento\Framework\GraphQl\Exception\GraphQlInputException(
                    __("Invalid '%1'", $val)
                );
            }
        }

        foreach ($validationEmpty as $val) {
            if (!isset($args[$val])) {
                throw new \Magento\Framework\GraphQl\Exception\GraphQlInputException(
                    __("Invalid '%1' is not empty", $val)
                );
            }
        }
    }
}
