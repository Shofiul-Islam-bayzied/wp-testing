<?php
/*
Plugin Name: A Reviews
Description:  reviews with reCAPTCHA and image upload support.
Version: 1.0
Author: bayzied
*/

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue Scripts and Styles
function apr_enqueue_scripts() {
    // Enqueue Google reCAPTCHA
    wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true);

    // Enqueue Swiper Slider
    wp_enqueue_style('swiper-style', 'https://unpkg.com/swiper/swiper-bundle.min.css');
    wp_enqueue_script('swiper-script', 'https://unpkg.com/swiper/swiper-bundle.min.js', array(), null, true);

    // Custom Script for Slider Initialization
    wp_add_inline_script('swiper-script', '
        document.addEventListener("DOMContentLoaded", function () {
            const swiper = new Swiper(".swiper-container", {
                loop: true,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
            });
        });
    ');
}
add_action('wp_enqueue_scripts', 'apr_enqueue_scripts');

// Shortcode to Display Review Form and Reviews
function apr_reviews_shortcode() {
    ob_start();
    ?>
    <div class="review-container">
        <h1>Apple Pencil Review</h1>
        <h2>Apple Pencil</h2>
        <p>Share your experience with the most advanced digital pencil</p>
        <p>Your feedback helps others make their creative journey better</p>

        <!-- Review Submission Form -->
        <form id="review-form" method="POST" enctype="multipart/form-data">
            <h3>Write Your Review</h3>

            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <label for="rating">Rating</label>
            <select id="rating" name="rating" required>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
            </select>

            <label for="title">Review Title</label>
            <input type="text" id="title" name="title" required>

            <label for="image">Add Photos</label>
            <p>Share your creative work (JPG, PNG - Max 5MB)</p>
            <input type="file" id="image" name="image" accept="image/jpeg, image/png">

            <label for="review">Your Review</label>
            <textarea id="review" name="review" required></textarea>

            <!-- Google reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_SITE_KEY"></div>

            <button type="submit">Submit Review</button>
        </form>

        <!-- Display Reviews -->
        <div id="reviews-slider" class="swiper-container">
            <div class="swiper-wrapper">
                <?php
                $reviews = apr_fetch_reviews_from_api();
                foreach ($reviews as $review) {
                    echo '<div class="swiper-slide">';
                    echo '<h3>' . esc_html($review['title']) . '</h3>';
                    echo '<p><strong>Rating:</strong> ' . esc_html($review['rating']) . '/5</p>';
                    echo '<p><strong>Review:</strong> ' . esc_html($review['review']) . '</p>';
                    if (!empty($review['image_path'])) {
                        echo '<img src="' . esc_url($review['image_path']) . '" alt="Review Image">';
                    }
                    echo '</div>';
                }
                ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('apple_pencil_reviews', 'apr_reviews_shortcode');

// Fetch Reviews from API
function apr_fetch_reviews_from_api() {
    $api_url = 'https://api-test.blubirdinteractive.org/api/reviews';
    $api_key = '$2a$12$WdgH3UCEBldog4tNNuXx3uNePxow63Wa3KFEzxa5BkdMq0vFKKuWy'; // Replace with your actual API key

    $response = wp_remote_get($api_url, array(
        'headers' => array(
            'Accept' => 'application/json',
            'X-API-Key' => $api_key,
        ),
    ));

    if (is_wp_error($response)) {
        return array(); // Return empty array on error
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    return isset($data['reviews']) ? $data['reviews'] : array();
}

// Handle Review Submission
function apr_handle_review_submission() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
        // Validate reCAPTCHA
        $recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);
        $recaptcha_secret = '6LeCGsgqAAAAAD-6ST54sCpo8-YenYq3fMStBLVb';
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

        $recaptcha_check = wp_remote_post($recaptcha_url, array(
            'body' => array(
                'secret' => $recaptcha_secret,
                'response' => $recaptcha_response,
            ),
        ));

        $recaptcha_result = json_decode(wp_remote_retrieve_body($recaptcha_check), true);

        if (!$recaptcha_result['success']) {
            wp_die('reCAPTCHA validation failed.');
        }

        // Process form data
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $rating = intval($_POST['rating']);
        $title = sanitize_text_field($_POST['title']);
        $review = sanitize_textarea_field($_POST['review']);

        // Handle image upload
        $image_url = '';
        if (!empty($_FILES['image']['tmp_name'])) {
            $uploaded_image = wp_upload_bits($_FILES['image']['name'], null, file_get_contents($_FILES['image']['tmp_name']));
            if (!$uploaded_image['error']) {
                $image_url = $uploaded_image['url'];
            }
        }

        // Send data to API
        $api_url = 'https://api-test.blubirdinteractive.org/api/reviews';
        $api_key = '$2a$12$WdgH3UCEBldog4tNNuXx3uNePxow63Wa3KFEzxa5BkdMq0vFKKuWy';

        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-API-Key' => $api_key,
            ),
            'body' => json_encode(array(
                'name' => $name,
                'email' => $email,
                'rating' => $rating,
                'title' => $title,
                'review' => $review,
                'image_path' => $image_url,
                'g-recaptcha-response' => $recaptcha_response,
            )),
        ));

        if (is_wp_error($response)) {
            wp_die('Error submitting review.');
        }

        wp_redirect(get_permalink()); // Redirect to refresh the page
        exit;
    }
}
add_action('init', 'apr_handle_review_submission');