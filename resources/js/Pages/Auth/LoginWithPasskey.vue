<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from "vue";
import ConfirmsPasskey from '@/Components/ConfirmsPasskey.vue';
import SectionBorder from '@/Components/SectionBorder.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const isPasswordLogin = ref(false);
const isProcessing = ref(false);

const passkeyConfirmation = ref(null);
const passwordInput = ref(null);

const form = useForm({
    email: '',
    password: '',
    passkey: '',
    remember: false,
});

const submit = () => {
    if(isPasswordLogin.value)
    {
        passwordLogin();
    }
    else
    {
        isProcessing.value = true;
        passkeyConfirmation.value.start(form.email);
    }
};

const passkeyLogin = () => {
    isProcessing.value = true;
    passkeyConfirmation.value.start(form.email);
}

const passwordLogin = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const cancelPasskeyLogin = (failed) => {
    if(!failed)
    {
        isPasswordLogin.value = true;
        setTimeout(() => passwordInput.value?.focus(), 100);
    }
    isProcessing.value = false
}
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form>
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
                <InputError class="mt-2" :message="passkeyConfirmation?.passkeyForm?.errors?.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    ref="passwordInput"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-center mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    Forgot your password?
                </Link>
                <span class="text-sm text-gray-600 dark:text-gray-400 px-2">|</span>
                <Link v-if="canResetPassword" :href="route('register')" class="ml-3 underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    Register
                </Link>

            </div>
            <PrimaryButton @click.prevent="passwordLogin" class="w-full mt-2 justify-center" :class="{ 'opacity-25': form.processing || isProcessing }" :disabled="form.processing || isProcessing">
                  Login
            </PrimaryButton>
            <div class="flex items-center justify-center my-2">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="px-2 text-gray-500">or</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>
            <PrimaryButton @click.prevent="passkeyLogin" class="w-full justify-center" :class="{ 'opacity-25': form.processing || isProcessing }" :disabled="form.processing || isProcessing">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                </svg>
                Login with Passkey
            </PrimaryButton>
            <ConfirmsPasskey :email="form.email" ref="passkeyConfirmation" @cancelled="cancelPasskeyLogin" mode="login" :remember="form.remember"/>
        </form>
    </AuthenticationCard>
</template>
