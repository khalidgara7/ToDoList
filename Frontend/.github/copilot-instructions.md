<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# Copilot Instructions for ToDo Frontend

This is a Vue.js 3 project with the following stack:
- Vue 3 with Composition API
- Pinia for state management
- Vue Router for navigation
- Axios for HTTP requests
- Vite as build tool

## Authentication System
- JWT-based authentication stored in localStorage
- User registration with: full name, email, phone, address, image, password
- Login with email and password
- Axios interceptors for automatic JWT header injection
- Form validation for all authentication forms

## Code Style Guidelines
- Use Composition API with `<script setup>`
- Use Pinia stores for state management
- Implement proper error handling
- Use async/await for API calls
- Follow Vue 3 best practices