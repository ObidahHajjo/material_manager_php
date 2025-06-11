<?php

namespace App\Requests;

class Request
{
    protected array $post;
    protected array $validatedData = [];
    public function __construct()
    {
        $this->post = $_POST;

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (
            str_starts_with($contentType, 'application/json') ||
            empty($_POST)
        ) {
            $rawInput = file_get_contents('php://input');
            $jsonData = json_decode($rawInput, true);

            if (is_array($jsonData)) {
                $this->post = $jsonData;
            }
        }
    }


    /**
     * Retrieve input using dot notation and wildcard support.
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        if (str_ends_with($key, '.*')) {
            $baseKey = substr($key, 0, -2);
            $subArray = $this->getNestedValue($this->post, $baseKey);
            return is_array($subArray) ? array_values($subArray) : $default;
        }

        return $this->getNestedValue($this->post, $key, $default);
    }

    protected function getNestedValue(array $array, string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        foreach ($segments as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }
        return $array;
    }

    public function validated(): array
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            header('Content-Type: application/json', true, 422);
            echo json_encode(['errors' => $errors], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }

        return $this->validatedData;
    }

    protected function validate(): array
    {
        $rules = $this->rules();
        $messages = $this->messages();
        $errors = [];

        foreach ($rules as $key => $ruleStr) {
            $ruleList = explode('|', $ruleStr);

            if (str_ends_with($key, '.*')) {
                $baseKey = substr($key, 0, -2);
                $items = $this->input($baseKey . '.*', []);

                if (!is_array($items)) {
                    $errors[$baseKey][] = "The {$baseKey} must be an array.";
                    continue;
                }

                foreach ($items as $index => $itemValue) {
                    foreach ($ruleList as $rule) {
                        if (!$this->applyRule("{$baseKey}.{$index}", $itemValue, $rule, $messages, $errors, "{$baseKey}.{$index}")) {
                            continue 2;
                        }
                    }

                    $this->validatedData[$baseKey][$index] = $this->castScalar($itemValue, $ruleList);
                }
            } else {
                $value = $this->input($key);

                foreach ($ruleList as $rule) {
                    if (!$this->applyRule($key, $value, $rule, $messages, $errors, $key)) {
                        continue 2;
                    }
                }

                $this->validatedData[$key] = $this->castScalar($value, $ruleList);
            }
        }

        return $errors;
    }

    protected function applyRule(string $field, mixed $value, string $rule, array $messages, array &$errors, string $errorKey): bool
    {
        [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);
        $passed = false;

        switch ($ruleName) {
            case 'required':
                $passed = !is_null($value) && $value !== '';
                break;
            case 'int':
                $passed = filter_var($value, FILTER_VALIDATE_INT) !== false;
                break;
            case 'float':
            case 'double':
                $passed = filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
                break;
            case 'timestamp':
                $passed = is_numeric($value) && ((int)$value >= 0) && (strtotime('@' . $value) !== false);
                break;
            case 'array':
                $passed = is_array($value);
                break;
            case 'string':
                $passed = is_string($value);
                break;
            case 'email':
                $passed = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            default:
                $passed = true;
                break;
        }

        if (!$passed) {
            $defaultMessages = $this->defaultMessages();

            $message = $messages["{$field}.{$ruleName}"]
                ?? $messages[$field]
                ?? str_replace(':field', $field, ($defaultMessages[$ruleName] ?? "The :field field failed the {$ruleName} validation."));

            $errors[$errorKey][] = $message;
        }

        return $passed;
    }

    protected function castScalar(mixed $value, array $rules): mixed
    {
        foreach ($rules as $rule) {
            switch ($rule) {
                case 'int':
                    return (int) $value;
                case 'float':
                case 'double':
                    return (float) $value;
                case 'timestamp':
                    return (int) $value;
            }
        }
        return $value;
    }

    protected function defaultMessages(): array
    {
        return [
            'required'   => 'The :field field is required.',
            'int'        => 'The :field must be an integer.',
            'float'      => 'The :field must be a decimal number.',
            'double'     => 'The :field must be a decimal number.',
            'array'      => 'The :field must be an array.',
            'string'     => 'The :field must be a string.',
            'timestamp'  => 'The :field must be a valid timestamp.',
            'email'      => 'The :field must be a valid email. '
        ];
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }
}
