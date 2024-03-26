<script setup>
    import {browserSupportsWebAuthn, startAuthentication } from "@simplewebauthn/browser";
    import { Vue3Lottie } from 'vue3-lottie'
    import DialogModal from './DialogModal.vue';
    import { ref } from 'vue';
    import { useForm, usePage } from '@inertiajs/vue3';

    const emit = defineEmits(['confirmed', 'cancelled']);

    const confirmingPasskey = ref(false);

    const authorityConfirmed = ref(null);

    const props = defineProps({
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
    });

    const operationCancelled = (failed=false) => {
        emit('cancelled', failed);
        confirmingPasskey.value = false;
    }

    const askForPasskey = () => {
        startAuthentication(JSON.parse(JSON.stringify(usePage().props.jetstream.flash.options)))
            .then((res) =>{
                passkeyForm.passkey = res;
                passkeyForm.post(route(props.mode == 'login' ? 'passkeys.login' : 'passkeys.verify'), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        if(props.mode == 'login' || usePage().props.jetstream.flash.verified) {
                            authorityConfirmed.value = true;
                        }
                    },
                    onError: (e) => {
                        console.error(e);
                        authorityConfirmed.value = false;
                    }
                });
            })
            .catch((e) => {
                console.log(e);
                authorityConfirmed.value = false;
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
                        setTimeout(askForPasskey, 250);
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
    <DialogModal :show="confirmingPasskey" @close="confirmingPasskey = false" ref="passkey">
        <template #title>
            {{ title }}
        </template>

        <template #content>
            {{ content }}
            <Vue3Lottie v-if="authorityConfirmed == null" animationLink="https://lottie.host/f33d7a7d-4521-4838-a056-42fdf900682e/tFltKtktYW.json" background="transparent" speed="1" :height="300" :width="300" style="width: 300px; height: 300px;" loop autoplay></Vue3Lottie>
            <Vue3Lottie v-else-if="authorityConfirmed" @onComplete="operationSuccess" animationLink="https://lottie.host/834754a6-57a2-47e9-8787-96a2de296977/GbEVvxtwjb.json" background="transparent" speed="1" :height="300" :width="300" style="width: 300px; height: 300px;" :loop="1" autoplay></Vue3Lottie>
            <Vue3Lottie v-else-if="authorityConfirmed == false" @onComplete="operationCancelled" animationLink="https://lottie.host/c99d756f-90a5-4fc8-a825-2adc03376435/sfGBEGnyKK.json" background="transparent" speed="1" :height="300" :width="300" style="width: 300px; height: 300px;" :loop="1" autoplay></Vue3Lottie>
        </template>
    </DialogModal>
</template>