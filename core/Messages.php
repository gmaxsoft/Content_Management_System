<?php

namespace Core;

/**
 * Notification messages: messages for one-time display using the session
 * for storage between requests.
 *
 */
class Messages
{
	const SUCCESS = 'success';
	const WARNING = 'warning';
	const INFO = 'info';

	public function __construct()
	{
		// Start session
		if (session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
			// session isn't started
			session_start();
		}
	}

	/**
	 * Add a message
	 *
	 * @param string $message The message content
	 *
	 * @return void
	 */
	public static function addMessage($message, $type = SELF::SUCCESS)
	{
		// Create array in the session if it doesn't already exist
		if (!isset($_SESSION['notifications'])) {
			$_SESSION['notifications'] = [];
		}
		// Append the message to the array
		$_SESSION['notifications'][] = [
			'text' => $message,
			'type' => $type
		];
	}

	public static function removeMessage()
	{
		if (isset($_SESSION['notifications'])) {
			unset($_SESSION['notifications']);
		}
	}

	/**
	 * Get all the messages
	 *
	 * @return mixed  An array with all the messages or null if none set
	 */
	public static function getMessages()
	{
		if (isset($_SESSION['notifications'])) {
			$messages = $_SESSION['notifications'];
			unset($_SESSION['notifications']);
			return $messages;
		}
	}
}
