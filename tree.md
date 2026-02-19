Models
📦Models
 ┣ 📜CampusCategory.php
 ┣ 📜CampusCourse.php
 ┣ 📜CampusCourseTeacher.php
 ┣ 📜CampusRegistration.php
 ┣ 📜CampusSeason.php
 ┣ 📜CampusStudent.php
 ┣ 📜CampusTeacher.php
 ┣ 📜Event.php
 ┣ 📜EventAnswer.php
 ┣ 📜EventQuestion.php
 ┣ 📜EventQuestionTemplate.php
 ┣ 📜EventType.php
 ┣ 📜FcmToken.php
 ┣ 📜Feedback.php
 ┣ 📜Notification.php
 ┣ 📜Setting.php
 ┣ 📜User.php
 ┗ 📜UserSetting.php


Controller
app/Http/Controllers
📦Campus
 ┣ 📜CategoryController.php
 ┣ 📜CourseController.php
 ┣ 📜RegistrationController.php
 ┣ 📜SeasonController.php
 ┣ 📜StudentController.php
 ┗ 📜TeacherController.php


Views
resources/views
📦views
 ┣ 📂admin
 ┃ ┣ 📂event-types
 ┃ ┃ ┣ 📜create.blade.php
 ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┗ 📜index.blade.php
 ┃ ┣ 📂events
 ┃ ┃ ┣ 📂answers
 ┃ ┃ ┃ ┣ 📜export-pdf.blade.php
 ┃ ┃ ┃ ┣ 📜index.blade.php
 ┃ ┃ ┃ ┗ 📜print.blade.php
 ┃ ┃ ┣ 📂event-question-templates
 ┃ ┃ ┃ ┣ 📜create.blade.php
 ┃ ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┃ ┗ 📜index.blade.php
 ┃ ┃ ┣ 📂questions
 ┃ ┃ ┃ ┣ 📜create.blade.php
 ┃ ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┃ ┗ 📜index.blade.php
 ┃ ┃ ┣ 📜create.blade.php
 ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┗ 📜index.blade.php
 ┃ ┣ 📂feedback
 ┃ ┃ ┗ 📜index.blade.php
 ┃ ┣ 📂permissions
 ┃ ┃ ┣ 📜create.blade.php
 ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┗ 📜index.blade.php
 ┃ ┣ 📂roles
 ┃ ┃ ┣ 📜create.blade.php
 ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┣ 📜form.blade.php
 ┃ ┃ ┗ 📜index.blade.php
 ┃ ┣ 📂users
 ┃ ┃ ┣ 📜create.blade.php
 ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┗ 📜index.blade.php
 ┃ ┗ 📜push-logs.blade.php
 ┣ 📂auth
 ┃ ┣ 📜confirm-password.blade.php
 ┃ ┣ 📜forgot-password.blade.php
 ┃ ┣ 📜login.blade.php
 ┃ ┣ 📜register.blade.php
 ┃ ┣ 📜reset-password.blade.php
 ┃ ┗ 📜verify-email.blade.php
 ┣ 📂calendar
 ┃ ┗ 📜index.blade.php
 ┣ 📂campus
 ┃ ┣ 📂categories
 ┃ ┃ ┣ 📜create.blade.php
 ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┣ 📜index.blade.php
 ┃ ┃ ┗ 📜show.blade.php
 ┃ ┣ 📂courses
 ┃ ┣ 📂registrations
 ┃ ┣ 📂seasons
 ┃ ┃ ┣ 📜create.blade.php
 ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┣ 📜index.blade.php
 ┃ ┃ ┗ 📜show.blade.php
 ┃ ┣ 📂shared
 ┃ ┃ ┗ 📜layout.blade.php
 ┃ ┣ 📂students
 ┃ ┗ 📂teachers
 ┣ 📂components
 ┃ ┣ 📂dashboard
 ┃ ┃ ┣ 📜admin.blade.php
 ┃ ┃ ┣ 📜adva-----nced.blade.php
 ┃ ┃ ┣ 📜basic.blade.php
 ┃ ┃ ┣ 📜card.blade.php
 ┃ ┃ ┣ 📜manager.blade.php
 ┃ ┃ ┣ 📜student.blade.php
 ┃ ┃ ┗ 📜teacher.blade.php
 ┃ ┣ 📜admin-dashboard-cards.blade.php
 ┃ ┣ 📜application-logo.blade.php
 ┃ ┣ 📜auth-session-status.blade.php
 ┃ ┣ 📜campus-button.blade.php
 ┃ ┣ 📜campus-color-select.blade.php
 ┃ ┣ 📜campus-danger-button.blade.php
 ┃ ┣ 📜campus-icon-select.blade.php
 ┃ ┣ 📜campus-parent-category-select.blade.php
 ┃ ┣ 📜campus-primary-button.blade.php
 ┃ ┣ 📜campus-secondary-button.blade.php
 ┃ ┣ 📜campus-yellow-button.blade.php
 ┃ ┣ 📜danger-button.blade.php
 ┃ ┣ 📜dropdown-link.blade.php
 ┃ ┣ 📜dropdown.blade.php
 ┃ ┣ 📜input-error.blade.php
 ┃ ┣ 📜input-label.blade.php
 ┃ ┣ 📜menu-admin.blade.php
 ┃ ┣ 📜menu-campus.blade.php
 ┃ ┣ 📜menu-user.blade.php
 ┃ ┣ 📜modal.blade.php
 ┃ ┣ 📜nav-link.blade.php
 ┃ ┣ 📜notification-bell.blade.php
 ┃ ┣ 📜primary-button.blade.php
 ┃ ┣ 📜responsive-nav-link.blade.php
 ┃ ┣ 📜secondary-button.blade.php
 ┃ ┗ 📜text-input.blade.php
 ┣ 📂debug
 ┃ ┗ 📜footer.blade.php
 ┣ 📂emails
 ┃ ┗ 📜notification.blade.php
 ┣ 📂layouts
 ┃ ┣ 📜app.blade.php
 ┃ ┣ 📜guest.blade.php
 ┃ ┗ 📜navigation.blade.php
 ┣ 📂notifications
 ┃ ┣ 📜create.blade.php
 ┃ ┣ 📜edit.blade.php
 ┃ ┣ 📜index.blade.php
 ┃ ┗ 📜show.blade.php
 ┣ 📂profile
 ┃ ┣ 📂partials
 ┃ ┃ ┣ 📜delete-user-form.blade.php
 ┃ ┃ ┣ 📜language-form.blade.php
 ┃ ┃ ┣ 📜update-password-form.blade.php
 ┃ ┃ ┗ 📜update-profile-information-form.blade.php
 ┃ ┗ 📜edit.blade.php
 ┣ 📂settings
 ┃ ┗ 📜edit.blade.php
 ┣ 📂vendor
 ┃ ┗ 📂pagination
 ┃ ┃ ┣ 📜bootstrap-4.blade.php
 ┃ ┃ ┣ 📜bootstrap-5.blade.php
 ┃ ┃ ┣ 📜default.blade.php
 ┃ ┃ ┣ 📜semantic-ui.blade.php
 ┃ ┃ ┣ 📜simple-bootstrap-4.blade.php
 ┃ ┃ ┣ 📜simple-bootstrap-5.blade.php
 ┃ ┃ ┣ 📜simple-default.blade.php
 ┃ ┃ ┣ 📜simple-tailwind.blade.php
 ┃ ┃ ┗ 📜tailwind.blade.php
 ┣ 📜dashboard.blade.php
 ┗ 📜welcome.blade.php