<script setup>
import { ref, computed } from 'vue';

const initial = computed(() => {
  if (state.user.name) {
    return state.user.name.charAt(0).toUpperCase();
  }
  return ''; // or any default value you prefer when name is null
});

const isOpen = ref(true)
const router = useRouter();

const logout = () => {
  router.push('/login/client');
};

const viewProfile = () => {
  if (state.user.role === 'client') {
    router.push('/client/settings');
  } else if (state.user.role === 'admin' || state.user.role === 'superadmin') {
    router.push('/admin/settings');
  }
};


const items = [
  [{
    label: 'View Profile',
    icon: 'i-lucide-circle-user-round',
    click: viewProfile
  },
  {
    label: 'Logout',
    icon: 'i-lucide-log-out',
    click: logout
  }],
]


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

    state.user.id = response.user_id;
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

    alert("Not logged in, log in first.");
    navigateTo('/');
  }
}

function handleSignOut() {
  localStorage.removeItem('_token');
  navigateTo('/');
}

onMounted(() => {
  fetchUser();
});
</script>

<template>
<div class="m-2 w-auto h-auto">
  <UDropdown 
    v-model="isOpen" 
    :items="items" 
    :popper="{ placement: 'right', arrow: 'true', offsetDistance: 10 }" 
    :ui="{background: 'dark:bg-custom-950', width: 'w-auto', arrow: {background: 'dark:before:bg-custom-400'}}" 
    class="w-full border rounded-sm border-custom-600 lg:block hidden" >
      <div class="w-auto flex justify-start items-center gap-2 p-4" >
        <div class="rounded-full bg-custom-400 dark:bg-custom-500 h-10 w-10 flex justify-center items-center">
          <p class="font-bold text-2xl m-auto">{{ initial }}</p>
        </div>
        <div class="border-l border-custom-400 pl-2 w-auto">
          <p class="tracking-wide lg:truncate lg:max-w-[150px] max-w-full capitalize">{{ state.user.name }}</p>
          <p class="text-custom-500 dark:text-custom-400 font-semibold text-sm tracking-wider">{{ state.user.role }}</p>
        </div>
      </div>
  </UDropdown>
  <div class="w-full border rounded-sm border-custom-600 flex justify-between lg:hidden">
    <div class="w-auto flex justify-start items-center gap-2 p-4 cursor-pointer" 
      @click="viewProfile">
      <div class="rounded-full bg-custom-400 dark:bg-custom-500 h-10 w-10 flex justify-center items-center">
        <p class="font-bold text-2xl m-auto">{{ initial }}</p>
      </div>
      <div class="border-l border-custom-400 pl-2 w-auto">
        <p class="tracking-wide lg:truncate lg:max-w-[150px] max-w-full capitalize">{{ state.user.name }}</p>
        <p class="text-custom-500 dark:text-custom-400 font-semibold text-sm tracking-wider">{{ state.user.role }}</p>
      </div>
    </div>
    <UButton 
      class="bg-red-500 dark:bg-red-500 hover:bg-red-600 h-10 mr-5 my-auto dark:hover:bg-red-400 text-white dark:text-white rounded" 
      label="Logout" 
      icon="i-lucide-log-out"
      to="/login/client" />
  </div>
</div>
</template>
