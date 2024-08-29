<template>
    <div>
        <div class="headerWelcome mb-3">
            <h2><i class="flaticon-381-diamond"></i> Creemos juntos tu experiencia de aprendizaje</h2>
            <span>Sigue esta lista de paso a paso para crear, compartir, y publicar tu curso</span>
        </div>

        <!-- CONTENIDO WELCOME -->
        <div class="contentWelcome">
            <div class="col-xl-12" v-for="(tarjeta, index) in tarjetas">
                <div class="card mb-3">
                    <div class="col-xl-2">
                        <i class="iconWelcome d-flex justify-content-center mt-1" :class="tarjeta.iconoTarjeta"></i>
                    </div>
                    <div class="col-xl-10">
                        <div class="card-header border-0 p-0 mt-3 tituloCard">
                            <div>Paso {{ index+1 }}</div>
                            <h5 class="card-title">{{ tarjeta.tituloTarjeta }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ tarjeta.contenidoTarjeta }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- FOOTER DE WELCOME -->
        <div class="footerWelcome">
            <div class="alert alert-warning alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                <strong>Una vez hayas creado!,</strong> publicado y compartido tu curso.
                <button type="button" class="close h-100" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                tarjetas: [
                    {
                        iconoTarjeta: 'flaticon-381-diamond',
                        tituloTarjeta: 'Crea Tu Primer Curso',
                        contenidoTarjeta: 'He lay on his armour-like back, and if he lifted his head a little he could see his brown belly, slightly domed and divided by arches into stiff'
                    },

                ]
            }
        },
    }
</script>

<style scoped>

</style>
