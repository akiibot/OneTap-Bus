<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

// Default Language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Handle Switch via URL
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    // Redirect to same page without query param to clean URL
    $current_url = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
    header("Location: $current_url");
    exit;
}

$en = [
    // Header
    'home' => 'Home',
    'about' => 'About Us',
    'signin' => 'Sign In',
    'dashboard' => 'Dashboard',
    'admin_panel' => 'Admin Panel',
    'logout' => 'Logout',

    // Home Hero
    'hero_title_prefix' => 'Your',
    'hero_title_accent' => 'ONE TAP',
    'hero_title_suffix' => 'solution <br>to find buses in Dhaka',
    'search_routes' => 'Search Routes',
    'view_all_buses' => 'View All Buses',

    // Search Box
    'where_to_go' => 'Where do you want to go?',
    'from' => 'From',
    'select_source' => 'Select source',
    'to' => 'To',
    'select_dest' => 'Select destination',
    'student_fare' => 'Student Fare?',
    'find_bus' => 'Find Bus 🔍',

    // Bus List
    'available_buses' => 'Available Buses',
    'no_buses' => 'No buses available.',
    'stops' => 'Stops',

    // Bus Details
    'operated_by' => 'Operated by',
    'route_info' => 'Route Information',
    'rate_this_bus' => 'Rate this Bus',
    'submit_review' => 'Submit Review',
    'write_exp' => 'Write your experience...',
    'user_reviews' => 'User Reviews',
    'no_reviews' => 'No reviews yet. Be the first!',
    'sign_in_to_rate' => 'Sign in to share your experience',
    'back_search' => 'Back to Search',
    'my_bookings' => 'My Bookings',
    'booking_id' => 'Booking ID',
    'seat' => 'Seat No',
    'date' => 'Date',
    'status' => 'Status',
    'cancel' => 'Cancel',
    'bookings_title' => 'My Booking History',
    'no_bookings' => 'You have no bookings yet.',

    // Search Results
    'search_results' => 'Search Results',
    'view_path' => '🗺️ View Path',
    'base_fare' => 'Base Fare',
    'reserve_seat' => '🎟️ Reserve Seat',
    'view_ticket' => 'View Ticket',
    'download_pdf' => 'Download PDF',

    // Auth
    'login_title' => 'Sign In',
    'signup_title' => 'Sign Up',
    'email' => 'Email',
    'password' => 'Password',
    'name' => 'Name',
    'account_type' => 'Account Type',
    'user_passenger' => 'User (Passenger)',
    'admin_manager' => 'Admin (Manager)',
    'admin_key' => 'Admin Authentication Key',
    'enter_key' => 'Enter secret key...',
    'create_account' => 'Create Account',
    'have_account' => 'Already have an account?',
    'no_account' => 'Don’t have an account?',
    'go_signin' => 'Sign in',
    'go_signup' => 'Sign up',

    // Booking
    'select_date' => 'Select Date',
    'seat_map' => 'Seat Map',
    'available' => 'Available',
    'booked' => 'Booked',
    'selected' => 'Selected',
    'total_price' => 'Total Price',
    'book_now' => 'Book Now',
    'proceed_pay' => 'Proceed to Pay',
    'driver' => 'Driver'
];

$bn = [
    // Header
    'home' => 'হোম',
    'about' => 'আমাদের সম্পর্কে',
    'signin' => 'লগ ইন',
    'dashboard' => 'ড্যাশবোর্ড',
    'admin_panel' => 'অ্যাডমিন প্যানেল',
    'logout' => 'লগ আউট',

    // Home Hero
    'hero_title_prefix' => 'ঢাকায় বাস খোঁজার',
    'hero_title_accent' => 'একমাত্র',
    'hero_title_suffix' => 'সহজ সমাধান',
    'search_routes' => 'রুট খুঁজুন',
    'view_all_buses' => 'সব বাস দেখুন',

    // Search Box
    'where_to_go' => 'আপনি কোথায় যেতে চান?',
    'from' => 'কোথা থেকে',
    'select_source' => 'উৎস নির্বাচন করুন',
    'to' => 'কোথায় যাবেন',
    'select_dest' => 'গন্তব্য নির্বাচন করুন',
    'student_fare' => 'ছাত্র ভাড়া?',
    'find_bus' => 'বাস খুঁজুন 🔍',

    // Bus List
    'available_buses' => 'উপলব্ধ বাসসমূহ',
    'no_buses' => 'কোন বাস পাওয়া যায়নি।',
    'stops' => 'টি স্টপেজ',

    // Bus Details
    'operated_by' => 'পরিচালনায়',
    'route_info' => 'রুটের তথ্য',
    'rate_this_bus' => 'রেটিং দিন',
    'submit_review' => 'রিভিউ জমা দিন',
    'write_exp' => 'আপনার অভিজ্ঞতা লিখুন...',
    'user_reviews' => 'ব্যবহারকারীদের মন্তব্য',
    'no_reviews' => 'এখনও কোন রিভিউ নেই। আপনিই প্রথম হন!',
    'sign_in_to_rate' => 'অভিজ্ঞতা শেয়ার করতে লগ ইন করুন',
    'back_search' => 'ফিরে যান',
    'my_bookings' => 'আমার টিকিটসমূহ',
    'booking_id' => 'বুকিং আইডি',
    'seat' => 'সিট নম্বর',
    'date' => 'তারিখ',
    'status' => 'অবস্থা',
    'cancel' => 'বাতিল করুন',
    'bookings_title' => 'বুকিং ইতিহাস',
    'no_bookings' => 'আপনার কোনো বুকিং নেই।',

    // Search Results
    'search_results' => 'অনুসন্ধানের ফলাফল',
    'view_path' => '🗺️ পথ দেখুন',
    'base_fare' => 'ভাড়া',
    'reserve_seat' => '🎟️ সিট বুক করুন',
    'view_ticket' => 'টিকিট দেখুন',
    'download_pdf' => 'পিডিএফ ডাউনলোড',

    // Auth
    'login_title' => 'লগ ইন',
    'signup_title' => 'সাইন আপ',
    'email' => 'ইমেইল',
    'password' => 'পাসওয়ার্ড',
    'name' => 'নাম',
    'account_type' => 'অ্যাকাউন্টের ধরন',
    'user_passenger' => 'ব্যবহারকারী (যাত্রী)',
    'admin_manager' => 'অ্যাডমিন (ম্যানেজার)',
    'admin_key' => 'অ্যাডমিন অথেনটিকেশন কি',
    'enter_key' => 'গোপন কি লিখুন...',
    'create_account' => 'অ্যাকাউন্ট তৈরি করুন',
    'have_account' => 'ইতোমধ্যে অ্যাকাউন্ট আছে?',
    'no_account' => 'কোনো অ্যাকাউন্ট নেই?',
    'go_signin' => 'লগ ইন করুন',
    'go_signup' => 'সাইন আপ করুন',

    // Booking
    'select_date' => 'তারিখ নির্বাচন করুন',
    'seat_map' => 'সিট ম্যাপ',
    'available' => 'খালি',
    'booked' => 'বুকড',
    'selected' => 'নির্বাচিত',
    'total_price' => 'মোট মূল্য',
    'book_now' => 'বুক করুন',
    'proceed_pay' => 'পেমেন্ট করুন',
    'driver' => 'ড্রাইভার'
];

// Set Global Language Array
$t = ($_SESSION['lang'] == 'bn') ? $bn : $en;
?>