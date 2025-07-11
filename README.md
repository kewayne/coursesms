# 📡 Course SMS – Send SMS Messages to Course Participants

![Moodle Plugin](https://img.shields.io/badge/Moodle-4.5+-blue.svg)
![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)

## 🔍 Overview

**Course SMS** is a Moodle [local plugin](https://moodledev.io/docs/apis/core/localplugins) that allows instructors and administrators to send SMS messages to participants of a course. It offers fine-grained targeting by **roles**, **groups**, or **all enrolled users**, with personalized messages and comprehensive logging of all sent communications.

This plugin is ideal for institutions that want to quickly reach students via mobile devices for urgent updates, deadlines, or announcements, even if users aren't logged in to Moodle.

---

## 🚀 Features

- ✅ Send SMS to:
  - All enrolled users
  - Specific roles (e.g., students, teachers)
  - Specific groups within a course
- ✅ Use placeholders like `{firstname}`, `{lastname}`, `{sender}`, `{coursename}` in message templates
- ✅ Display real-time character count in form
- ✅ View logs of all sent messages
- ✅ Expandable log view with success/failure breakdown
- ✅ Built-in permissions for fine-grained capability control
- ✅ Compatible with Moodle 4.5+

---

## 🧑‍🏫 Target Audience

- Moodle Administrators
- Course Creators
- Teachers and Trainers
- Organizations seeking SMS-based outreach

---

## 📦 Installation

1. Clone or download this plugin and place it in the `local/` directory:

   ```bash
   cd /path/to/your/moodle/local
   git clone https://github.com/kewayne/moodle-local_coursessms coursessms
