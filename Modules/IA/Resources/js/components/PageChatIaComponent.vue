<template>
   <div>
    <main class="__variable_ccb6bc font-sans __variable_7f3d6a bg-slate-900 tracking-[0.01em]">
        <main class="bg-gradient-radial from-[#0d153d] to-[#02060F]  h-full relative">
            <div class="relative pt-8 w-full justify-center flex items-center pb-20 moveHeaderAnimation dev_moveHeaderAnimation z-30 px-4" data-initalquestionasked="false">
                <div class="w-full flex flex-col items-center mb-4 md:mt-2">
                    <div style="opacity: 1; transform: none;">
                        <img alt="" loading="lazy" width="350" height="0" decoding="async" data-nimg="1" class="w-[320px] md:w-[340px] md:-mt-0 relative z-10 h-auto" style="color:transparent" src="img/logo_klaxinn.png">
                    </div>
                    <div class="flex flex-col items-center w-full max-w-[560px]" style="opacity: 1;">
                        <div class="flex rounded-md shadow-sm mt-4 md:mt-5 justify-center w-full">
                            <div class="relative w-full">
                                <textarea @keypress.enter="SendMessageServer" @input="handleInput" v-model="text" class="block w-full py-3 rounded-none rounded-l-xl bg-white border-gray-300 pl-4 pr-2 border-none focus:outline-none resize-none h-17 md:h-12" placeholder="Describe tu situacion o realiza una pregunta..."></textarea>
                            </div>
                            <div class="bg-gray-25 rounded-r-3xl">
                                <button @click="SendMessageServer"  class="relative -ml-px inline-flex items-center space-x-2 rounded-xl border border-[#7A61C8] bg-gradient-to-br from-[#A186F4] to-[#553BBF] px-8 h-full text-md font-medium text-gray-100 hover:to-[#553BBF] hover:from-[#AF97FA] transition-all">
                                    <span>Preguntar</span>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="flex justify-center">
                <div class="h-0 opacity-0 showCardAnimation overflow-hidden max-w-[52rem] w-full px-3 absolute bottom-0 dev_showCardAnimation" data-initalquestionasked="true">
                    <div v-if="messages.length != 0">
                        <div class="w-full bg-gray-100 rounded-3xl px-4 pt-2 md:pt-8 pb-4 md:px-8 shadow-lg shadow-gray-800 z-30">
                            <div v-for="message in messages">
                                <!-- Primera sección con texto y SVG -->
                                <div v-if="message.type == 1" class="flex w-full text-xl md:text-2xl justify-end py-4 pl-4 md:pl-8 md:mb-2 text-right text-gray-700 items-star ">
                                    <p class="">{{ message.text }}</p>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon" class="w-6 h-6 mt-[1.2px] md:w-8 md:h-8 ml-2 text-gray-600 shrink-0"><path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd"></path></svg>
                                </div>
                            
                                <!-- Segunda sección con texto y logotipo de Bible Ai -->
                                <div v-if="message.type == 2" class="flex text-left items-start py-1 md:py-6 mb-6 justify-start font-medium text-gray-900 text-xl md:text-2xl pr-4 md:pr-8">
                                    <div style="width: 35px;margin-right: 10px;" >
                                        <svg version="1.1"  id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
        viewBox="0 0 512 512" style="enable-background:new 0 0 512 512; width: 35px;margin-right: 10px;" xml:space="preserve">
    <g>
    <g>
        <path d="M477.867,221.698c-15.87,0-29.206,10.5-33.01,25.433H409.35c-1.977-36.267-16.367-68.438-38.938-93.644l25.156-25.073
            c5.197,3.075,11.121,4.792,17.317,4.792c9.117,0,17.689-3.529,24.134-9.975c6.448-6.448,9.999-15.008,9.999-24.125
            c0-9.119-3.551-17.683-9.998-24.129c-6.447-6.45-15.019-9.996-24.135-9.996c-9.118,0-17.69,3.55-24.135,9.996
            c-6.448,6.448-9.998,15.021-9.998,24.14c0,6.196,1.676,12.121,4.75,17.317l-25.03,25.154
            c-25.207-22.571-57.419-36.96-93.685-38.938V67.144c14.933-3.804,25.474-17.142,25.474-33.011C290.259,15.313,274.883,0,256.063,0
            c-18.821,0-34.038,15.313-34.038,34.133c0,15.869,10.761,29.206,25.695,33.011v35.506c-36.267,1.977-68.729,16.367-93.936,38.938
            l-25.219-25.154c3.074-5.196,4.719-11.121,4.719-17.317c0-9.119-3.567-17.69-10.013-24.135
            c-6.447-6.448-15.027-9.998-24.144-9.998c-9.118,0-17.693,3.55-24.14,9.998c-6.447,6.446-10,15.017-10,24.135
            c0,9.117,3.55,17.687,9.996,24.133c6.448,6.448,15.019,9.998,24.135,9.998c6.196,0,12.121-1.675,17.317-4.748l25.156,24.988
            c-22.573,25.206-36.963,57.377-38.941,93.644H67.144c-3.805-14.933-17.141-25.433-33.011-25.433
            C15.313,221.698,0,237.096,0,255.917c0,18.821,15.313,34.006,34.133,34.006c15.87,0,29.205-10.792,33.011-25.725h35.505
            c1.978,36.267,16.368,68.771,38.939,93.979l-25.154,25.238c-5.196-3.073-11.121-4.706-17.317-4.706
            c-9.117,0-17.689,3.571-24.135,10.019c-6.447,6.446-9.998,15.027-9.998,24.146c0,9.117,3.55,17.696,9.998,24.142
            c6.654,6.652,15.395,9.983,24.135,9.983c8.74,0,17.482-3.327,24.136-9.981c6.447-6.446,9.998-15.017,9.998-24.133
            c0-6.198-1.676-12.121-4.749-17.317l25.279-25.154c25.207,22.571,57.671,36.96,93.938,38.937v35.506
            c-14.933,3.804-25.726,17.142-25.726,33.01c0,18.821,15.249,34.133,34.07,34.133c18.821,0,34.228-15.312,34.228-34.133
            c0-15.869-10.572-29.206-25.505-33.01V409.35c36.267-1.977,68.479-16.367,93.685-38.94l25.092,25.156
            c-3.073,5.196-4.78,11.121-4.78,17.317c0,9.117,3.535,17.69,9.982,24.135c6.654,6.654,15.385,9.981,24.127,9.981
            c8.741,0,17.478-3.327,24.132-9.981c6.447-6.446,9.996-15.019,9.996-24.135c0-9.119-3.552-17.69-9.999-24.135
            c-6.447-6.448-15.019-9.998-24.135-9.998c-6.196,0-12.121,1.675-17.318,4.748l-25.155-25.321
            c22.572-25.208,36.96-57.712,38.937-93.979h35.506c3.804,14.933,17.141,25.767,33.01,25.767c18.821,0,34.133-15.312,34.133-34.133
            C512,237.011,496.688,221.698,477.867,221.698z M400.817,87.048c3.223-3.223,7.508-4.998,12.068-4.998
            c4.558,0,8.844,1.775,12.068,5c3.223,3.221,4.999,7.506,4.999,12.067c0,4.558-1.776,8.844-5,12.067
            c-3.223,3.223-7.508,4.998-12.067,4.998c-4.559,0-8.845-1.775-12.068-4.998l-0.001-0.002c-3.223-3.221-4.998-7.506-4.998-12.064
            C395.818,94.556,397.593,90.271,400.817,87.048z M111.186,111.181l-0.001,0.002c-3.223,3.223-7.508,4.998-12.068,4.998
            c-4.558,0-8.845-1.775-12.069-4.998c-3.223-3.223-4.998-7.508-4.998-12.067c0-4.56,1.775-8.846,4.999-12.069
            c3.223-3.223,7.51-4.998,12.068-4.998c4.559,0,8.845,1.775,12.069,5c3.223,3.221,4.999,7.506,4.999,12.067
            C116.185,103.675,114.408,107.961,111.186,111.181z M34.133,273.067c-9.41,0-17.067-7.656-17.067-17.067
            c0-9.41,7.656-17.067,17.067-17.067c9.411,0,17.067,7.656,17.067,17.067C51.2,265.411,43.544,273.067,34.133,273.067z
                M111.186,424.952c-6.655,6.652-17.483,6.652-24.137,0c-3.224-3.225-4.999-7.51-4.999-12.069c0-4.56,1.775-8.846,4.999-12.069
            c3.223-3.223,7.51-4.998,12.068-4.998c4.559,0,8.845,1.775,12.068,4.998l0.001,0.002c3.223,3.221,4.999,7.506,4.999,12.067
            C116.185,417.442,114.408,421.727,111.186,424.952z M400.816,400.817l0.001-0.002c3.223-3.223,7.508-4.998,12.068-4.998
            c4.558,0,8.844,1.775,12.068,5c3.223,3.221,4.999,7.506,4.999,12.067c0,4.558-1.776,8.844-4.999,12.069
            c-6.656,6.652-17.483,6.652-24.135,0c-3.224-3.225-4.999-7.51-4.999-12.069C395.818,408.323,397.593,404.038,400.816,400.817z
                M238.933,34.133c0-9.41,7.656-17.067,17.067-17.067c9.411,0,17.067,7.656,17.067,17.067c0,9.411-7.656,17.067-17.067,17.067
            C246.59,51.2,238.933,43.544,238.933,34.133z M273.067,477.867c0,9.411-7.656,17.067-17.067,17.067
            c-9.41,0-17.067-7.656-17.067-17.067c0-9.41,7.656-17.067,17.067-17.067C265.411,460.8,273.067,468.456,273.067,477.867z
                M256,392.533c-75.284,0-136.533-61.248-136.533-136.533c0-75.285,61.249-136.533,136.533-136.533
            c75.284,0,136.533,61.248,136.533,136.533C392.533,331.286,331.285,392.533,256,392.533z M477.867,273.067
            c-9.41,0-17.067-7.656-17.067-17.067c0-9.41,7.656-17.067,17.067-17.067c9.411,0,17.067,7.656,17.067,17.067
            C494.933,265.411,487.277,273.067,477.867,273.067z"/>
    </g>
    </g>
    <g>
    <g>
        <path d="M238.933,162.133c-28.232,0-51.2,22.969-51.2,51.2c0,28.231,22.968,51.2,51.2,51.2s51.2-22.969,51.2-51.2
            C290.133,185.102,267.166,162.133,238.933,162.133z M238.933,247.467c-18.821,0-34.133-15.312-34.133-34.133
            c0-18.821,15.313-34.133,34.133-34.133c18.821,0,34.133,15.313,34.133,34.133C273.067,232.154,257.754,247.467,238.933,247.467z"
            />
    </g>
    </g>
    <g>
    <g>
        <path d="M273.067,281.6c-18.821,0-34.133,15.313-34.133,34.133c0,18.821,15.313,34.133,34.133,34.133
            c18.821,0,34.133-15.312,34.133-34.133C307.2,296.913,291.888,281.6,273.067,281.6z M273.067,332.8
            c-9.41,0-17.067-7.656-17.067-17.067c0-9.41,7.656-17.067,17.067-17.067c9.411,0,17.067,7.656,17.067,17.067
            C290.133,325.144,282.477,332.8,273.067,332.8z"/>
    </g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
    <g>
    </g>
                                        </svg>
                                    </div>

                                    <div v-if="message.text == ''" >
                                        <div class="dev_loader">
                                            <div class="loader"></div>
                                        </div>
                                    </div>

                                    <div v-if="message.text != ''">
                                        <p>{{ message.text }}</p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
            
        </main>
    </main> 
   </div> 
</template>

<script>
import Pusher from 'pusher-js';
export default {
    
    props:
    {
        gpt: String
    },
    created()
    {
        document.getElementsByClassName("navbar-nav")[0].remove();
        document.getElementsByClassName("header")[0].remove();
        document.getElementsByClassName("nav-header")[0].remove();
    },
    async mounted()
    {
        console.log('componente montado')
        var pusher = new Pusher('185a1191ff877f11d545', {
            cluster: 'us2'
        });
        var channel = pusher.subscribe('gpt_audiid_channel');
        var context = this;
        channel.bind('gp_audiid_event', function(data) 
        {
            // console.log(data);
            if(data != "")
            {
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth'
                });
                // context.gpt_loading = false;
                // if(context.full_chat == '')
                // {
                //     context.full_chat += data;
                //     context.messages.push({type: 2, text: context.full_chat});
                // }
                // else
                // {
                    context.full_chat += data;
                    context.messages[context.messages.length - 1].text = context.full_chat;
                // }

                // console.log(this.full_chat)
                // console.log(`${data}`)
            }

        });
    },
    data()
    {
        return {
            config:
            {
                url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
                token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            text:"",
            skeleton_detail: true,
            gpt_loading: false,
            full_chat: "",
            messages: []
        }
    },
    methods:
    {
        async SendMessageServer()
        {
            try 
            {
                if(this.text == "")
                {
                    toastr.warning(`Debes realizar alguna consulta`);
                    return;
                }
                if(this.text.split(/\s+/).length < 3)
                {
                    toastr.warning(`Coloca almenos 3 palabras para tu consulta`);
                    return;
                }
                let data_form = new FormData();
                data_form.append('user_message', Object.assign(this.text));
                data_form.append('phone', "3224922195");
                data_form.append('phone_question', "3127379236");
                data_form.append('mode', 1);

                this.messages.push({type: 1, text: this.text });
                this.messages.push({type: 2, text: "" });
                this.text = "";
                this.full_chat = "";
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth'
                });

                this.gpt_loading = true;
                let rs = await fetch(`${this.gpt}ia/chat/message_api`, { method: "POST", body: data_form});
                let rd = await rs.json();

                const { success, message, data, responseCode } = rd;

                switch (responseCode) 
                {
                    case 200:
                        break;
                
                    default:
                        break;
                }

            } 
            catch (error) 
            {
                console.error(`Error al traer datos: ${error.message}`);            
            }

        },
        handleInput(event) {
            // Eliminar los saltos de línea del texto
            this.text = event.target.value.replace(/(\r\n|\n|\r)/gm, '');
        }
    }
}
</script>

<style scoped>
</style>