import { reactive } from "vue";

// Initialize the user object
export const user = reactive({
  first_name: '',
  last_name: '',
  mi: "l",
  gender: "male",
  phone: '',

  // statically change it for now just to test (client, admin, superadmin)
  role: '',

  status: "active",
  username: "superadmin123",
  password: "password",
  account_created: "2024/05/26",
  updated_at: "2024/05/26",
});

// Dynamically compute the full name
user.name = `${user.first_name} ${user.mi}. ${user.last_name}`;
