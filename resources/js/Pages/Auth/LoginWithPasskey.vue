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

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const isPasswordLogin = ref(false);
const isProcessing = ref(false);

const passkeyConfirmation = ref(null);

const form = useForm({
    __USERNAME__: '',
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
        passkeyConfirmation.value.start(form.__USERNAME__);
    }
};

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
        isPasswordLogin.value = true
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

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="__USERNAME__" value="__USERNAME_LABEL__" />
                <TextInput
                    id="__USERNAME__"
                    v-model="form.__USERNAME__"
                    type="__USERNAME_TYPE__"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.__USERNAME__" />
                <InputError class="mt-2" :message="passkeyConfirmation?.passkeyForm?.errors?.__USERNAME__" />
            </div>

            <div class="mt-4" v-if="isPasswordLogin">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
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

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    Forgot your password?
                </Link>
                <span class="ml-3 text-sm">or</span>
                <Link v-if="canResetPassword" :href="route('register')" class="ml-3 underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    Register
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing || isProcessing }" :disabled="form.processing || isProcessing">
                    {{ isPasswordLogin ? 'Login' : 'Next' }}
                </PrimaryButton>
            </div>
        </form>
        <ConfirmsPasskey :__USERNAME__="form.__USERNAME__" ref="passkeyConfirmation" @cancelled="cancelPasskeyLogin" mode="login" :remember="form.remember"/>
    </AuthenticationCard>
</template>
