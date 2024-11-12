<script setup>
    import {browserSupportsWebAuthn, startAuthentication } from "@simplewebauthn/browser";
    import DialogModal from './DialogModal.vue';
    import { ref } from 'vue';
    import { useForm, usePage } from '@inertiajs/vue3';

    const emit = defineEmits(['confirmed', 'cancelled']);

    const confirmingPasskey = ref(false);

    const authorityConfirmed = ref(null);

    const props = defineProps({
        remember: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: 'Confirm Passkey',
        },
        content: {
            type: String,
            default: 'For your security, please confirm your passkey to continue.',
        },
        __USERNAME__: {
            type: String,
            default: '',
        },
        mode: {
            type: String,
            default: 'verify',
            validate: (value) => ['login', 'verify'].indexOf(value) !== -1
        }
    })

    const passkeyForm = useForm({
        passkey: '',
        __USERNAME__: props.__USERNAME__,
        remember: false,
    });

    const operationCancelled = (failed=false) => {
        emit('cancelled', failed);
        confirmingPasskey.value = false;
    }

    const askForPasskey = () => {
        startAuthentication(JSON.parse(JSON.stringify(usePage().props.jetstream.flash.options)))
            .then((res) =>{
                passkeyForm.passkey = res;
                passkeyForm.transform(data => ({
                        ...data,
                        remember: props.remember ? 'on' : '',
                    })).post(route(props.mode == 'login' ? 'passkeys.login' : 'passkeys.verify'), {
                            preserveScroll: true,
                            preserveState: true,
                            onSuccess: () => {
                                if(props.mode == 'login' || usePage().props.jetstream.flash.verified) {
                                    authorityConfirmed.value = true;
                                    operationSuccess();
                                }
                            },
                            onError: (e) => {
                                console.error(e);
                                authorityConfirmed.value = false;
                                operationCancelled();
                            }
                });
            })
            .catch((e) => {
                console.log(e);
                authorityConfirmed.value = false;
                operationCancelled();
            })
    }

    defineExpose({
        passkeyForm: passkeyForm,
        start: (__USERNAME__ = null) => {
            if(!browserSupportsWebAuthn()) {
                emit('cancelled');
            }
            if(__USERNAME__)
                passkeyForm.__USERNAME__ = __USERNAME__;
                passkeyForm.post(route('passkeys.authentication-options'), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    if(!usePage().props.jetstream.flash.options) {
                        emit('cancelled');
                    }
                    else
                    {
                        authorityConfirmed.value = null;
                        confirmingPasskey.value = true;
                        askForPasskey();
                    }
                },
                onError: (e) => {
                    operationCancelled(true);
                }
            });
        },
    });


    const operationSuccess = () => {
        confirmingPasskey.value = false;
        emit('confirmed');
    }

</script>
<template>
   
</template>