# 📡 Course SMS – Send SMS Messages to Course Participants

![Moodle Plugin](https://img.shields.io/badge/Moodle-4.5+-blue.svg)
![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)

## 🔍 Overview  
Course SMS is a Moodle local plugin that empowers instructors and administrators to send SMS messages directly to course participants from within Moodle. This tool provides a direct and immediate communication channel, perfect for urgent announcements, reminders, or personalized messages.

The plugin features a user-friendly interface that allows for sending messages to all participants, or targeting specific roles (like all students) or groups within a course. All sent messages are logged with detailed reports on successful and failed deliveries, providing a complete audit trail.

---

## ⚠️ Important: Requires an SMS Gateway  
This plugin facilitates the sending of SMS messages from Moodle, but it does **not** send SMS messages for free. To use this plugin, you must have an active account with a third-party SMS gateway provider.

Moodle's SMS system supports various gateways, which you can configure in:  
**Site administration > Messaging > SMS gateways.**

You will need to obtain API credentials from your chosen provider and enter them into Moodle. Without a configured and enabled gateway, this plugin will not be able to send any messages.

---

## 🚀 Key Features  

- ✅ **Targeted Messaging:** Send SMS messages to:  
  - All enrolled participants in a course.  
  - Users with a specific role (e.g., all 'Students' or 'Teachers').  
  - Members of a specific group within the course.

- ✅ **Personalized Messages:** Use placeholders like `{firstname}`, `{lastname}`, `{sender}`, and `{coursename}` to automatically insert recipient and course details into your messages.

- ✅ **Comprehensive Logging:** A detailed log of all sent messages is maintained, including:  
  - Who sent the message and when.  
  - The message content.  
  - The targeted group or role.  
  - A list of successful and failed deliveries.

- ✅ **Permissions Control:** The plugin includes capabilities (`local/coursessms:sendsms` and `local/coursessms:viewlog`) to control who can send messages and view logs. By default, this is available to Managers, Course Creators, and Teachers.

- ✅ **Seamless Integration:** Adds a "Course SMS" link to the course navigation for easy access.

---

## 🧑‍🏫 Target Audience  
- **Moodle Administrators:** For site-wide implementation of SMS communication.  
- **Course Creators & Teachers:** To directly and quickly communicate with their students.  
- **Educational Institutions:** That need a reliable and fast way to send announcements and alerts.

---

## 📦 Installation & Setup  

1. **Download the Plugin:** Download the plugin ZIP file.

2. **Install the Plugin:**  
   - Log in to your Moodle site as an administrator.  
   - Go to **Site administration > Plugins > Install plugins.**  
   - Upload the ZIP file. Default installation settings are usually fine.  
   - Moodle will guide you through the installation process, including upgrading the database.

3. **Alternative: Manual Installation:**  
   - Unzip the plugin files.  
   - Rename the plugin folder to `coursessms`.  
   - Upload the `coursessms` folder to the `local/` directory of your Moodle installation.  
   - As an administrator, go to **Site administration > Notifications** to trigger the installation.

4. **Configure Permissions:**  
   - Go to **Site administration > Users > Permissions > Define roles.**  
   - For roles that should send SMS (e.g., 'Teacher'), allow `local/coursessms:sendsms`.  
   - For roles that should view logs, allow `local/coursessms:viewlog`.

5. **Set up SMS Gateway:**  
   - This is critical. Go to **Site administration > Messaging > SMS gateways.**  
   - Select and configure your chosen SMS gateway provider with the API credentials they provide.  
   - Without this step, the plugin will not function.

---

## 📖 How to Use  

1. **Navigate to a Course:** Go to the Moodle course from which you want to send an SMS.

2. **Open Course SMS:** In the course navigation menu, click on **"Course SMS"**.

3. **Compose Your Message:**  
   - Two tabs appear: **Send SMS** and **SMS Log**.  
   - On **Send SMS**, select your target audience (All participants, Role, or Group).  
   - If Role or Group is selected, a dropdown will appear to select the specific one.  
   - Type your message in the **Message content** box. Use placeholders as needed.  
   - Click **Send SMS**.

4. **Check the Log:**  
   - Click the **SMS Log** tab to see all messages sent for that course.  
   - Click **View details** on any entry to see full content and delivery status.  
   - Failed deliveries usually indicate missing phone numbers in user profiles.

---

## ⚙️ Technical Details  

- **Database Tables:**  
  - `local_coursessms_log`: Stores records of every SMS batch sent.

- **Capabilities:**  
  - `local/coursessms:sendsms`: Allows sending SMS messages within a course.  
  - `local/coursessms:viewlog`: Allows viewing SMS logs for a course.

---

## 📄 License  
This plugin is licensed under the **GNU GPL v3 License**.

