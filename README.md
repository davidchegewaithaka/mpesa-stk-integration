# M-Pesa STK Push Integration

This project demonstrates integration with Safaricom's M-Pesa Daraja API for STK Push payments using PHP and MySQL. It includes:

- Initiating STK Push payment requests
- Handling asynchronous callback responses
- Storing transaction data in a MySQL database
- Displaying transaction history in a responsive Bootstrap-styled webpage

## Technologies Used

- PHP
- MySQL
- Bootstrap 5
- Ngrok (for exposing local server during development)
- Postman (for API testing)

## Setup Instructions

1. Clone the repo:
   ```bash
   git clone https://github.com/davidchegewaithaka/mpesa-stk-integration.git
   cd mpesa-stk-integration
2. Set up the MySQL database:
  - Create a database called mpesa_db
  - Run the SQL script to create the mpesa_transactions table
3. Update your db.php file with your database credentials.
4. Start your local server (e.g., using XAMPP or built-in PHP server)
5. Start Ngrok to expose your local server (replace your port if different)
  - ngrok http 80
6. Update your M-Pesa API credentials and the callback URL with your Ngrok URL.
7. Test the STK Push using Postman or the Safaricom sandbox environment.
8. Visit http://localhost:8000/transactions.php (or your server URL) to view transaction records.

# File Structure
  - stk_push.php — initiates STK Push requests
  - callback.php — receives and processes payment callbacks
  - db.php — database connection
  - transactions.php — displays transactions in a Bootstrap table
  - README.md — this file
# Notes
  - Make sure your Ngrok URL is public and accessible for the Safaricom server to send callbacks.
  - The project currently uses the Safaricom sandbox environment.
# License
  - This project is open-source and available under the MIT License.
