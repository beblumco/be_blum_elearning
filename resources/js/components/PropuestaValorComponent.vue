<template>
       <div class="col-xl-6 col-xxl-6 col-lg-6">
         <div class="card">
           <div class="card-header  border-0 pb-0" style="display: block;">
             <div class="row">
               <div class="col-sm-8">
                 <h4 class="card-title">Propuesta de valor</h4>
               </div>
               <div class="col-sm-4">
                 <a href="#" class="dev-see-all" @click.prevent.stop="OnClickRedirectDetail()">Ver todo</a>
               </div>
             </div>
           </div>
           <div class="card-body"> 
             <div id="DZ_W_Todo1" class="widget-media dz-scroll height370" @scroll="OnScrollView($event)" v-if="section_value_proposal.items != undefined">
               <ul class="timeline" id="content-value-proposal" ref="content_value_proposal">
                   <MiniSectionPropuestaValorComponent v-for="item in section_value_proposal.items" :key="item.id" :data_proposal="item"></MiniSectionPropuestaValorComponent>
               </ul>
               <MiniLoadingComponent :is_show="section_value_proposal.paginate.show_loading"></MiniLoadingComponent>
             </div>

             <div id="DZ_W_Todo1" class="widget-media dz-scroll height370" v-else>
               <ul class="timeline" id="content-value-proposal" ref="content_value_proposal">
                <li>
                    <p class="text-center font-weight-bold">Sin información</p>
                </li>
               </ul>
             </div>

             </div>
           </div>
        </div>
</template>
<script>
import MiniSectionPropuestaValorComponent from "./MiniSectionPropuestaValorComponent.vue";
import MiniLoadingComponent from "./MiniLoadingComponent.vue";

export default 
{
    props:{
    },
    components: {
        MiniSectionPropuestaValorComponent,
        MiniLoadingComponent
    },
    async mounted() 
    {
        await this.InitDataPage();
    },
    data(){
        return {
            token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            url: document.querySelector('meta[name="csrf-token"]').getAttribute("url"),
            section_value_proposal: {
                items: [],
                paginate: {
                    currentPage: 1,
                    lastPage: 1,
                    show_loading: false
                }
            }
        }
    },
    methods: {
        async InitDataPage(page = 1)
        {
            try 
            {
                let rs = await fetch(`${this.url}dashboard/data?page=${page}`, { method: "GET", headers: {
                    'X-CSRF-TOKEN': this.token 
                }});
                let rd = await rs.json();
                
                this.section_value_proposal.paginate.currenPage = rd.current_page;
                this.section_value_proposal.paginate.lastPage = rd.last_page;

                if(page == 1)
                    this.section_value_proposal.items = rd.data;
                else
                    this.section_value_proposal.items.push.apply(this.section_value_proposal.items, rd.data);

            } 
            catch (error) 
            {
                console.error(`Error para traer la data de la sección propuesta de valor: ${error.message}`);                    
            }
        },
        OnClickRedirectDetail()
        {
            return;
            // window.location.href = `${this.url}dashboard/propuesta_detalle`;
        },
        async OnScrollView(e)
        {
            let element = e.target;
            let scrollPosition = element.scrollTop + element.offsetHeight;
            let contentProposal = this.$refs.content_value_proposal.offsetHeight;

            if((scrollPosition) == contentProposal && this.section_value_proposal.paginate.currentPage < this.section_value_proposal.paginate.lastPage)
            {
                this.section_value_proposal.paginate.currentPage += 1;
                this.section_value_proposal.paginate.show_loading = true;
                await this.InitDataPage(this.section_value_proposal.paginate.currentPage);
                this.section_value_proposal.paginate.show_loading = false;
            }
        }
    }
}
</script>

<style scoped>
.dev-see-all
{
    color: #FE634E;
    font-weight: 600;
}
.card-title
{
    text-transform: none !important;
}
</style>