<?php
/*
Plugin Name: OpenAI Plugin

Description: A plugin that allows users to ask OpenAI and display the answer on the website
Version: 1.0

*/
function openai_enqueue_styles() {
  wp_enqueue_style('openai-style', plugins_url('css/style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'openai_enqueue_styles');

function openai_shortcode() {
  return '
    <form method="post">
      <label for="job-title">Job Title:</label><br>
      <input type="text" id="job-title" name="job-title"><br>
      <input type="submit" value="Submit">
    </form>';
}
add_shortcode('openai', 'openai_shortcode');

function openai_process_input() {
  if (isset($_POST['job-title'])) {
    $job_title = sanitize_text_field($_POST['job-title']);

    $response = wp_remote_post(
      'https://api.openai.com/v1/docs/',
      array(
        'headers' => array(
          'Authorization' => 'Bearer YOUR-API-KEY',
          'Content-Type' => 'application/json'
        ),
        'body' => json_encode(array(
          'model' => 'text-davinci-002',
          'prompt' => 'Write an "about" section for a person with the job title "' . $job_title . '".'
        ))
      )
    );

    if (is_wp_error($response)) {
      $error_message = $response->get_error_message();
      return '<div>Something went wrong: ' . $error_message . '</div>';
    } else {
      $response_body = json_decode($response['body'], true);
      return '<div>' . $response_body['data']['response'] .
      '</div>';
    }
  }
}
add_action('wp_footer', 'openai_process_input');
