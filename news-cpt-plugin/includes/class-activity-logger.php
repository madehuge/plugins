<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class Activity_Logger {
    protected $user_id;
    protected $activity;

    // Constructor to initialize the activity logger
    public function __construct($user_id, $activity) {
        $this->user_id = $user_id;
        $this->activity = $activity;

        // Initialize the database connection (Eloquent ORM)
        $this->initDatabase();
    }

    // Initialize the Eloquent ORM database connection
    private function initDatabase() {
        global $wpdb;
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => $wpdb->prefix,
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    // Method to log the activity
    public function logActivity() {
        global $wpdb;
        Capsule::table($wpdb->prefix . 'user_activity_logs')->insert([
            'user_id' => $this->user_id,
            'activity' => $this->activity,
            'activity_time' => current_time('mysql'),
        ]);
    }

    // Method to get activities for a user
    public static function getActivities($user_id) {
        global $wpdb;
        return Capsule::table($wpdb->prefix . 'user_activity_logs')
                      ->where('user_id', $user_id)
                      ->get();
    }
}
