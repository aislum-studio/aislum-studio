# Aislum Studio - UI/UX Design for Authentication and Dashboard

This document outlines the UI/UX design for the core authentication (login, registration) and dashboard pages of the Aislum Studio web application, leveraging the PICO CSS framework for a clean, responsive, and accessible interface.

## 1. General Design Principles

*   **Minimalism**: Utilize PICO CSS's inherent minimalist design to keep the interface uncluttered and focused on content.
*   **Responsiveness**: Ensure all pages adapt seamlessly to various screen sizes (desktop, tablet, mobile).
*   **Accessibility**: Adhere to basic accessibility standards, including clear form labels, sufficient color contrast, and keyboard navigation.
*   **Consistency**: Maintain a consistent look and feel across all pages using PICO CSS components.

## 2. Login Page Design

**Objective**: Provide a clear and secure way for users to access their accounts.

**Layout**: Centered form within a `container` for focus. The form will use PICO CSS's `article` element for a card-like appearance.

**Components**:

*   **Header**: Application logo/name (`Aislum Studio`) and a clear title (`Login`).
*   **Form Fields**:
    *   `Username or Email` (text input)
    *   `Password` (password input)
*   **Call to Action**: `Login` button (primary style).
*   **Links**: `Forgot Password?` (future feature), `Don't have an account? Register here.`
*   **Flash Messages**: Display success/error messages (e.g., 
Invalid credentials`) using PICO CSS alert styles.

**Example Structure (HTML/PICO CSS)**:

```html
<main class="container">
  <article class="grid">
    <div>
      <hgroup>
        <h1>Login</h1>
        <h2>Aislum Studio</h2>
      </hgroup>
      <form action="?page=auth&action=login" method="POST">
        <input type="text" name="username_email" placeholder="Username or Email" aria-label="Username or Email" autocomplete="username" required>
        <input type="password" name="password" placeholder="Password" aria-label="Password" autocomplete="current-password" required>
        <button type="submit" class="contrast">Login</button>
      </form>
      <small>
        <a href="#">Forgot Password?</a><br>
        <a href="?page=auth&action=register">Don't have an account? Register here.</a>
      </small>
    </div>
  </article>
</main>
```

## 3. Registration Page Design

**Objective**: Allow new users to create an account with necessary details.

**Layout**: Similar to the login page, a centered form within a `container` and `article`.

**Components**:

*   **Header**: Application logo/name (`Aislum Studio`) and a clear title (`Register`).
*   **Form Fields**:
    *   `Full Name` (text input)
    *   `Username` (text input)
    *   `Email` (email input)
    *   `Password` (password input)
    *   `Confirm Password` (password input)
*   **Call to Action**: `Register` button (primary style).
*   **Links**: `Already have an account? Login here.`
*   **Flash Messages**: Display success/error messages (e.g., `Registration successful!`) using PICO CSS alert styles.

**Example Structure (HTML/PICO CSS)**:

```html
<main class="container">
  <article class="grid">
    <div>
      <hgroup>
        <h1>Register</h1>
        <h2>Aislum Studio</h2>
      </hgroup>
      <form action="?page=auth&action=register" method="POST">
        <input type="text" name="full_name" placeholder="Full Name" aria-label="Full Name" required>
        <input type="text" name="username" placeholder="Username" aria-label="Username" autocomplete="username" required>
        <input type="email" name="email" placeholder="Email" aria-label="Email" autocomplete="email" required>
        <input type="password" name="password" placeholder="Password" aria-label="Password" autocomplete="new-password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" aria-label="Confirm Password" autocomplete="new-password" required>
        <button type="submit" class="contrast">Register</button>
      </form>
      <small>
        <a href="?page=auth&action=login">Already have an account? Login here.</a>
      </small>
    </div>
  </article>
</main>
```

## 4. Dashboard Page Design

**Objective**: Provide a personalized overview for the logged-in user, with quick access to key features.

**Layout**: A main content area with a grid-based layout for widgets or quick links. The navigation bar (from `layout.php`) will be visible.

**Components**:

*   **Header**: `Welcome, [Username]!` or `Dashboard`.
*   **Overview Section**: Display summary information (e.g., number of documents, recent designs, unread notifications).
*   **Quick Access Cards/Widgets**:
    *   `My Documents`: Link to document management.
    *   `My Designs`: Link to design tools.
    *   `Upload New Document` / `Create New Design` buttons.
    *   `Recent Activity`: A small feed of recent user actions.
*   **Navigation**: The global navigation bar will provide links to Documents, Designs, and Profile.

**Example Structure (HTML/PICO CSS)**:

```html
<main class="container">
  <hgroup>
    <h1>Welcome, <?php echo sanitize($_SESSION["username"]); ?>!</h1>
    <h2>Your Aislum Studio Dashboard</h2>
  </hgroup>

  <div class="grid">
    <article>
      <h3>My Documents</h3>
      <p>Manage your office documents.</p>
      <footer>
        <a href="?page=documents" role="button">View Documents</a>
      </footer>
    </article>

    <article>
      <h3>My Designs</h3>
      <p>Create and edit business cards and other designs.</p>
      <footer>
        <a href="?page=designs" role="button">View Designs</a>
      </footer>
    </article>

    <article>
      <h3>Quick Actions</h3>
      <ul>
        <li><a href="?page=documents&action=upload">Upload New Document</a></li>
        <li><a href="?page=designs&action=create">Create New Design</a></li>
      </ul>
    </article>

    <article>
      <h3>Recent Activity</h3>
      <small>No recent activity.</small>
      <!-- Future: Dynamically load recent activities -->
    </article>
  </div>
</main>
```

## 5. Styling Considerations

*   **PICO CSS Classes**: Utilize PICO CSS classes like `container`, `grid`, `article`, `hgroup`, `button`, `contrast`, `outline`, `secondary` for consistent styling.
*   **Custom CSS**: A `public/css/custom.css` file will be used for minor overrides or specific styles not covered by PICO CSS, ensuring the app maintains its unique branding while benefiting from the framework.
*   **Flash Messages**: Implement dynamic display of flash messages using the `getAllFlashMessages()` helper function and PICO CSS alert styles (e.g., `.alert-success`, `.alert-error`).

This design provides a solid foundation for implementing the frontend of the authentication and dashboard features. The next step will be to translate these designs into functional PHP views and integrate them with the backend logic.
