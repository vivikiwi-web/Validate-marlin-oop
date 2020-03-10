<?php

/**
 * Класс для валидации данных
 */
class Validate
{
    private $_passed = false, $_errors = [], $_pdo = null;

    /**
     * Метод для проверки волидации. 
     *
     * @param array $source
     * @param array $items
     * @return void
     */
    public function check($source, $items = [])
    {
        $this->_errors = [];

        foreach ($items as $item => $rules) {
            $item = $this->sanitize($item);
            $display = $rules['display'];

            foreach ($rules as $rule => $rule_value) {
                $value = $this->sanitize(trim($source[$item]));

                if ($rule === 'required' && empty($value)) {
                    $this->addError(["{$display} is required", $item]);
                } else if (!empty($value)) {
                    switch ($rule) {
                        case 'min':
                            if (strlen($value) < $rule_value) {
                                $this->addError(["{$display} must be a minimum of {$rule_value} charecters.", $item]);
                            }
                            break;

                        case 'max':
                            if (strlen($value) > $rule_value) {
                                $this->addError(["{$display} must be a maximum of {$rule_value} charecters.", $item]);
                            }
                            break;

                        case 'matches':
                            if ($value != $source[$rule_value]) {
                                $matchDisplay = $items[$rule_value]['display'];
                                $this->addError(["{$matchDisplay} and {$display} must match.", $item]);
                            }
                            break;

                        case 'is_numeric':
                            if (!is_numeric($value)) {
                                $this->addError(["{$display} has to be a number. Please use a numeric value.", $item]);
                            }

                            break;

                        case 'valid_email':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $this->addError(["{$display} must be a valid email address.", $item]);
                            }

                            break;
                    }
                }
            }
        }

        if (empty($this->_errors)) {
            $this->_passed = true;
        }

        return $this;
    }

    /**
     * Медод для полухения ошибок валидации
     *
     * @return void
     */
    public function error()
    {
        return $this->_errors;
    }

    /**
     * Медод для получения информции о пройдиной валидации
     *
     * @return void
     */
    public function passed()
    {
        return $this->_passed;
        echo $this->_passed;
    }

    /**
     * Метод для отображения ошибок валидации
     *
     * @return void
     */
    public function displayErrors()
    {
        $html = '';

        if ($this->_errors) {
            $html = '<div id="dispay_error"><ul class="alert alert-danger">';
            foreach ($this->_errors as $error) {
                if (is_array($error)) {
                    $html .= '<li>' . $error[0] . '</li>';
                } else {
                    $html .= '<li>' . $error . '</li>';
                }
            }
            $html .= '</ul></div>';
        }

        return $html;
    }

    /**
     * Медод для добобления ошубок валидации
     *
     * @param array|string $error
     * @return void
     */
    private function addError($error)
    {
        $this->_errors[] = $error;
        if (empty($this->_errors)) {
            $this->_passed = true;
        } else {
            $this->_passed = false;
        }
    }

    /**
     * Саницизация инпута
     *
     * @param string $dirty
     * @return void
     */
    private function sanitize($dirty)
    {
        return htmlentities($dirty, ENT_QUOTES, "UTF-8");
    }
}
