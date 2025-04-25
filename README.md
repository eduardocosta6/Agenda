# Agenda - Personal Event Management System

Agenda is a lightweight, user-friendly web application for managing personal events and appointments. It provides a clean interface for users to create, view, edit, and delete their events through both a list view and an interactive calendar.



## Features

- **User Authentication**: Secure login and registration system
- **Personal Events**: Events created by users are only visible to them
- **Calendar View**: Interactive monthly calendar for visualizing events
- **Upcoming Events**: Home page displays a timeline of upcoming events
- **Event Management**: Create, edit, and delete events with ease
- **Responsive Design**: Works on desktop and mobile devices

## Technologies Used

- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Icons**: Font Awesome
- **UI Design**: Custom CSS with responsive design

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

### Setup Instructions

1. **Clone the repository**

   ```
   git clone https://github.com/yourusername/agenda.git
   cd agenda
   ```

2. **Create the database**

   ```sql
   CREATE DATABASE agenda;
   USE agenda;
   ```

3. **Import the database schema**

   ```
   mysql -u username -p agenda < database/schema.sql
   ```

4. **Configure the database connection**

   - Edit `config/database.php` with your database credentials:

   ```php
   $host = 'localhost';
   $username = 'your_username';
   $password = 'your_password';
   $database = 'agenda';
   ```

5. **Set up your web server**

   - Configure your web server to point to the project directory
   - Ensure the `logs` directory is writable by the web server

6. **Access the application**
   - Open your browser and navigate to the application URL
   - Register a new account and start managing your events

## Usage

### Registration and Login

- New users can register with a name, email, and password
- Existing users can log in with their email and password

### Managing Events

- **View Events**: The home page displays upcoming events in chronological order
- **Calendar View**: Navigate to the Calendar page to see events in a monthly view
- **Create Event**: Click on a day in the calendar to add an event for that day
- **Edit/Delete**: Each event has options to edit or delete it

### Calendar Navigation

- Use the navigation buttons to move between months
- Click on any day to add an event for that specific date

## Project Structure

```
agenda/
├── assets/
│   └── css/
│       ├── calendar.css
│       ├── navbar.css
│       └── style.css
├── components/
│   └── navbar.php
├── config/
│   └── database.php
├── includes/
│   ├── logger.php
│   └── session.php
├── logs/
├── add_event.php
├── calendar.php
├── delete_event.php
├── edit_event.php
├── index.php
├── login.php
├── logout.php
├── register.php
└── README.md
```

## Security Features

- Password hashing using PHP's password_hash() function
- Prepared statements to prevent SQL injection
- Session management for user authentication
- Input validation and sanitization

## Future Enhancements

- Event categories and color coding
- Email notifications and reminders
- Recurring events
- Sharing events with other users
- Dark mode theme option

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Font Awesome for the icons
- The PHP community for excellent documentation
- All contributors who have helped improve this project
