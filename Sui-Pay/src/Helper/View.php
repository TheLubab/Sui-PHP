<?php

namespace SuiPay\Helper;

/**
 * Method to render template
 *
 * @param $templatePath
 * @return bool
 */
function render_view(string $templatePath, $args = null): bool
{
  try {
    if (!file_exists($templatePath)) {
      throw new \Exception("Template file does not exist: $templatePath");
    }

    // Extract variables if $args is provided
    if ($args !== null && is_array($args)) {
      extract($args);
    }

    ob_start();
    require($templatePath);
    $output = ob_get_clean();

    if (!empty($output)) {
      echo wp_kses($output, getAllowedTagsForFormEscape());
      return true;
    }

    throw new \Exception("Template rendered an empty content: $templatePath");
  } catch (\Exception $e) {
    echo esc_html($e->getMessage());
  }
  return false;
}


function getAllowedTagsForFormEscape(): array
{
  $allowedAtts = [
    'align' => [],
    'class' => [],
    'type' => [],
    'id' => [],
    'dir' => [],
    'lang' => [],
    'style' => [],
    'xml:lang' => [],
    'src' => [],
    'alt' => [],
    'href' => [],
    'rel' => [],
    'rev' => [],
    'target' => [],
    'novalidate' => [],
    'value' => [],
    'name' => [],
    'tabindex' => [],
    'action' => [],
    'method' => [],
    'for' => [],
    'width' => [],
    'height' => [],
    'data' => [],
    'title' => [],
    'selected' => [],
    'placeholder' => [],
    'min' => [],
    'max' => [],
    'checked' => [],
    'selected' => [],
    'onclick' => [],
    'required' => [],
    'data-amount' => [],
    'data-receiver' => [],
  ];
  return [
    'h1' => $allowedAtts,
    'label' => $allowedAtts,
    'form' => $allowedAtts,
    'input' => $allowedAtts,
    'button' => $allowedAtts,
    'div' => $allowedAtts,
    'select' => $allowedAtts,
    'option' => $allowedAtts,
    'script' => $allowedAtts,
    'span' => $allowedAtts,
    'a' => $allowedAtts,
    'br' => $allowedAtts,
    'p' => $allowedAtts,
    'style' => $allowedAtts,
  ];
}
