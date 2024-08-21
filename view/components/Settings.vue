<template>
  <div class="lg:h-screen h-auto w-full p-5 gap-5 flex flex-col justify-between">
    <div class="flex-col gap-5 flex">

      <header class="flex justify-between w-full items-center">
        <h1 class="text-lg font-semibold text-custom-800 dark:text-white">Settings</h1>
        <UButton
          class="bg-red-500 dark:bg-red-500 hover:bg-red-600 dark:hover:bg-red-400 text-white dark:text-white rounded"
          label="Logout" icon="i-lucide-log-out" @click="handleSignOut" />
      </header>

      <main
        class="flex flex-col h-auto gap-5 p-5 rounded dark:bg-custom-900 bg-custom-100 border border-custom-300 dark:border-custom-700">

        <div class="flex justify-between items-center">
          <h1 class="font-semibold text-lg">Profile</h1>
          <UButton v-if="user.role === 'admin' || user.role === 'superadmin'"
            class="flex cursor-pointer justify-center items-center rounded dark:text-white'" label="Edit"
            icon="i-lucide-edit" :to="editProfileRoute" />
          <UButton v-if="user.role === 'client'"
            class="flex cursor-pointer justify-center items-center rounded dark:text-white'" label="Edit"
            icon="i-lucide-edit" :to="editProfileRoute" />
        </div>

        <hr class="dark:border-custom-700 border-custom-200">

        <div class="md:flex grid gap-10 h-auto w-full">

          <section
            class="w-52 h-52 rounded-full dark:bg-custom-700 md:mx-10 mx-auto bg-custom-400 flex justify-center items-center cursor-default">
            <span class="font-black text-[150px]">{{ initial }}</span>
          </section>

          <section class="my-auto">
            <div class="grid grid-cols-5 gap-5 my-2">
              <h1 class="capitalize col-span-2">Name: </h1>
              <p class="col-span-3 dark:text-custom-300 text-custom-500">{{ state.user.name }}</p>
            </div>

            <div class="grid grid-cols-5 gap-5 my-2">
              <h1 class="capitalize col-span-2">Gender: </h1>
              <p class="col-span-3 dark:text-custom-300 text-custom-500">{{ state.user.gender }}</p>
            </div>

            <div class="grid grid-cols-5 gap-5 my-2">
              <h1 class="capitalize col-span-2">Phone No.: </h1>
              <p class="col-span-3 dark:text-custom-300 text-custom-500">{{ state.user.phone_no }}</p>
            </div>

            <div class="grid grid-cols-5 gap-5 my-2">
              <h1 class="capitalize col-span-2">Role: </h1>
              <p class="col-span-3 dark:text-custom-300 text-custom-500">{{ state.user.role }}</p>
            </div>

            <div class="grid grid-cols-5 gap-5 my-2">
              <h1 class="capitalize col-span-2">Status: </h1>
              <div class="col-span-3 dark:text-custom-300 text-custom-500 capitalize">
                <UKbd v-if="state.user.status === 'active'"
                      class="dark:border bg-green-600 dark:border-green-700 text-custom-100 dark:text-green-400 cursor-default px-2"
                      :value="state.user.status" />
                <UKbd v-else-if="state.user.status === 'inactive'"
                      class="dark:border bg-red-600 dark:border-red-700 text-custom-100 dark:text-red-400 cursor-default px-2"
                      :value="state.user.status" />
                <p v-else>{{ state.user.status }}</p>
              </div>
            </div>
          </section>

        </div>
      </main>

      <div class="grid md:grid-cols-2 grid-cols-1 h-auto w-full justify-between gap-5">

        <section
          class="h-auto w-full dark:bg-custom-900 bg-custom-100 flex flex-col gap-5 p-5 rounded border border-custom-300 dark:border-custom-700">
          <h1 class="font-semibold text-lg">Login Credentials</h1>
          <hr class="dark:border-custom-700 border-custom-200">
          <section class="my-auto">
            <div class="grid grid-cols-3 gap-5 my-2">
              <h1 class="capitalize col-span-1">Username: </h1>
              <p class="col-span-2 dark:text-custom-300 text-custom-500">{{ state.user.username }}</p>
            </div>

            <div class="grid grid-cols-3 gap-5 my-2">
              <h1 class="capitalize col-span-1">Password: </h1>
              <p class="col-span-2 dark:text-custom-300 text-custom-500">{{ user.password }}</p>
            </div>
          </section>
        </section>

        <section
          class="h-auto w-full dark:bg-custom-900 bg-custom-100 flex flex-col gap-5 p-5 rounded border border-custom-300 dark:border-custom-700">
          <h1 class="font-semibold text-lg">Timestamps (date & time)</h1>
          <hr class="dark:border-custom-700 border-custom-200">
          <section class="my-auto">
            <div class="grid grid-cols-5 gap-5 my-2">
              <h1 class="capitalize col-span-2">Account Created: </h1>
              <p class="col-span-3 dark:text-custom-300 text-custom-500">{{ state.user.created_at }}</p>
            </div>

            <div class="grid grid-cols-5 gap-5 my-2">
              <h1 class="capitalize col-span-2">Last Update: </h1>
              <p class="col-span-3 dark:text-custom-300 text-custom-500">{{ state.user.updated_at }}</p>
            </div>
          </section>
        </section>

      </div>
    </div>
    <Footer />
  </div>
</template>

<script setup>
import { user } from '~/assets/js/userSample';

const state = reactive({
  user: {
    user_id: null,
    name: null,
    gender: null,
    phone_no: null,
    username: null,
    created_at: null,
    updated_at: null,
    status: null,
    role: null,
  },
  errors: null,
});

async function fetchUser() {
  const params = {
    Authorization: "Bearer " + localStorage.getItem('_token'),
    Accept: "application/json"
  };
  
  try {
    const response = await $fetch(`http://127.0.0.1:8000/api/user`, {
      method: 'GET',
      headers: params
    });

    state.user.user_id = response.user_id;
    state.user.name = response.first_name + ' ' + response.last_name;
    state.user.gender = response.gender;
    state.user.phone_no = response.phone_no;
    state.user.role = response.role;
    state.user.status = response.status;
    state.user.updated_at = response.updated_at;
    state.user.username = response.username;
    state.user.created_at = response.created_at;

  } catch (error) {
    state.errors = error.response;
    console.log(error.response);
    console.log('error', error);
  }
}

function handleSignOut() {
  localStorage.removeItem('_token');
  navigateTo('/');
}

onMounted(() => {
  fetchUser();
  
});



const initial = computed(() => state.user.name ? state.user.name.charAt(0).toUpperCase() : '');
const route = useRoute();
const { id } = route.params;
const editProfileRoute = computed(() => {
  if (state.user.role === 'admin' || state.user.role === 'superadmin') {
    return `/admin/settings/update/${state.user.user_id}`;
  } else if (state.user.role === 'client') {
    return `/client/settings/update/${state.user.user_id}`;
  }
});
</script>
