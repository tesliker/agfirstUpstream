<?php

namespace Drupal\markdown_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;
use Parsedown;

/**
 * Plugin implementation of the 'markdown_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "markdown_formatter",
 *   label = @Translation("Markdown"),
 *   field_types = {
 *     "string",
 *     "string_long"
 *   },
 *   settings = {
 *     "allowed_tags" = "<sup> <sub>",
 *     "trim_length" = "",
 *     "trim_suffix" = "",
 *   }
 * )
 */
class MarkdownFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'allowed_tags' => '<sup> <sub>',
        'trim_length' => '',
        'trim_suffix' => ''
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {

    $trim_length = $this->getSetting('trim_length');
    $trim_suffix = $this->getSetting('trim_suffix');

    $summary = [];
    $summary[] = $this->t('Displays a markdown filtered string.');

    if (!empty(trim($trim_length))) {
      $settings_summary = $trim_length . ' characters';
      if (!empty(trim($trim_suffix))) {
        $settings_summary .= $this->t(' with suffix');
      }
      $summary[] = $settings_summary;
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    $allowed_tags = $this->getSetting('allowed_tags');

    foreach ($items as $delta => $item) {

      // Ensure that only accepted html tags sneak through.
      $value = strip_tags($item->value, $allowed_tags);

      // Parse the markdown.
      $parsedown = new Parsedown();
      $parsed_value = $parsedown->text($value);

      $trim_length = $this->getSetting('trim_length');
      if (is_numeric(trim($trim_length))) {
        $trim_suffix = $this->getSetting('trim_suffix');
        $parsed_value = $this->truncate($parsed_value, intval($trim_length), $trim_suffix);
      }

      // Render each element as markup.
      $element[$delta] = [
        '#markup' => $parsed_value,
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $element['allowed_tags'] = [
      '#title' => $this->t('Allowed HTML tags'),
      '#type' => 'textfield',
      '#description' => $this->t('A list of HTML tags that can be used in addition to <a href="https://www.markdownguide.org/cheat-sheet" target="_blank">Markdown</a> syntax.'),
      '#default_value' => $this->getSetting('allowed_tags'),
      '#maxlength' => 255,
      '#required' => FALSE,
    ];

    $element['trim_length'] = [
      '#title' => $this->t('Trim length'),
      '#type' => 'textfield',
      '#description' => $this->t('Number of characters to output. Leave blank for no limit.'),
      '#default_value' => $this->getSetting('trim_length'),
      '#size' => 10,
      '#maxlength' => 10,
      '#required' => FALSE,
    ];

    $element['trim_suffix'] = [
      '#title' => $this->t('Suffix'),
      '#type' => 'textfield',
      '#description' => $this->t('Text to add to the end of trimmed content.'),
      '#default_value' => $this->getSetting('trim_suffix'),
      '#size' => 10,
      '#maxlength' => 50,
      '#required' => FALSE,
    ];

    return $element;
  }

  /**
   * Truncates text.
   *
   * Cuts a string to the length of $length and replaces the last characters
   * with the ending if the text is longer than length.
   *
   * @param string $text String to truncate.
   * @param integer $length Length of returned string, including ellipsis.
   * @param string $ending Ending to be appended to the trimmed string.
   * @param boolean $exact If false, $text will not be cut mid-word
   * @param boolean $considerHtml If true, HTML tags would be handled correctly
   *
   * @return string Trimmed string.
   */
  private function truncate($text, $length, $ending = '...', $exact = TRUE, $considerHtml = TRUE) {
    if ($considerHtml) {
      // if the plain text is shorter than the maximum length, return the whole text
      if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
        return $text;
      }

      // splits all html-tags to scanable lines
      preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

      $total_length = strlen($ending);
      $open_tags = [];
      $truncate = '';

      foreach ($lines as $line_matchings) {
        // if there is any html-tag in this line, handle it and add it (uncounted) to the output
        if (!empty($line_matchings[1])) {
          // if it’s an “empty element” with or without xhtml-conform closing slash (f.e.)
          if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
            // do nothing
            // if tag is a closing tag (f.e.)
          }
          else {
            if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
              // delete tag from $open_tags list
              $pos = array_search($tag_matchings[1], $open_tags);
              if ($pos !== FALSE) {
                unset($open_tags[$pos]);
              }
              // if tag is an opening tag (f.e. )
            }
            else {
              if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                // add tag to the beginning of $open_tags list
                array_unshift($open_tags, strtolower($tag_matchings[1]));
              }
            }
          }
          // add html-tag to $truncate’d text
          $truncate .= $line_matchings[1];
        }

        // calculate the length of the plain text part of the line; handle entities as one character
        $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
        if ($total_length + $content_length > $length) {
          // the number of characters which are left
          $left = $length - $total_length;
          $entities_length = 0;
          // search for html entities
          if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
            // calculate the real length of all entities in the legal range
            foreach ($entities[0] as $entity) {
              if ($entity[1] + 1 - $entities_length <= $left) {
                $left--;
                $entities_length += strlen($entity[0]);
              }
              else {
                // no more characters left
                break;
              }
            }
          }
          $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
          // maximum lenght is reached, so get off the loop
          break;
        }
        else {
          $truncate .= $line_matchings[2];
          $total_length += $content_length;
        }

        // if the maximum length is reached, get off the loop
        if ($total_length >= $length) {
          break;
        }
      }
    }
    else {
      if (strlen($text) <= $length) {
        return $text;
      }
      else {
        $truncate = substr($text, 0, $length - strlen($ending));
      }
    }

    // if the words shouldn't be cut in the middle...
    if (!$exact) {
      // ...search the last occurrence of a space...
      $space_position = strrpos($truncate, ' ');
      if (isset($space_position)) {
        // ...and cut the text in this position
        $truncate = substr($truncate, 0, $space_position);
      }
    }

    // add the defined ending to the text
    $truncate = trim($truncate) . $ending;

    if ($considerHtml) {
      // close all unclosed html-tags
      foreach ($open_tags as $tag) {
        $truncate .= '';
      }
    }

    return $truncate;

  }

}
