<template>
  <div class="btn-group btn dropdown col-lg-12 p-0" v-if="options">

    <!-- Dropdown Input -->
    <input class="dropdown-input form-control"
        :id = "id"
      :name="name"
      @focus="showOptions()"
      @blur="exit()"
      @keyup="keyMonitor"
      v-model="searchFilter"
      :disabled="disabled"
      autocomplete="off"
      :placeholder="placeholder" />


    <!-- Dropdown Menu -->
    <div class="dropdown-content col-lg-12 p-0"
      v-show="optionsShown">
      <div
        class="dropdown-item"
        @mousedown="selectOption(option)"
        v-for="(option, index) in filteredOptions"
        :key="index">
          {{ option.name || option.id || '-' }}
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'Dropdown',
    template: 'Dropdown',
    props: {
      name: {
        type: String,
        required: false,
        default: 'dropdown',
        note: 'Input name'
      },
      id: {
        type: String,
        required: false,
        default: '',
        note: 'id Input'
      },
      options: {
        type: Array,
        required: true,
        default: () => [],
        note: 'Options of dropdown. An array of options with id and name',
      },
      placeholder: {
        type: String,
        required: false,
        default: 'Please select an option',
        note: 'Placeholder of dropdown'
      },
      disabled: {
        type: Boolean,
        required: false,
        default: false,
        note: 'Disable the dropdown'
      },
      maxItem: {
        type: Number,
        required: false,
        default: 6,
        note: 'Max items showing'
      },
      change: {
        type: String,
        required: false,
        default: "",
        note: "Allow event change"
      },
      orderBy:
      {
        type: Object, // { order: "DESC || ASC", property: "id || name" }
        required: false,
        default: undefined,
        note: "Order by type selected"
      }

    },
    data() {
      return {
        selected: {},
        optionsShown: false,
        searchFilter: ''
      }
    },
    created() {
      this.$emit('selected', this.selected);
      //console.log(this.options);
    },
    computed: {
      filteredOptions()
      {
        if(this.orderBy != undefined)
        {
          return (this.orderBy.order.toUpperCase() == "ASC"
            ? this.options.sort((a,b) => a[this.orderBy.property] - b[this.orderBy.property])
            : this.options.sort((a,b) => b[this.orderBy.property] - a[this.orderBy.property])
          );
        }

        const filtered = [];
        // const regOption = new RegExp(this.searchFilter, 'ig');
        const regOption = new RegExp(this.escapeRegExp(this.searchFilter), 'ig');
        for (const option of this.options) {
          if (this.searchFilter.length < 1 || option.name.match(regOption)){
            if (filtered.length < this.maxItem){
                filtered.push(option);
                filtered.sort((a, b) => {
                    const aContainsSearchTerm = a.name.includes(this.searchFilter);
                    const bContainsSearchTerm = b.name.includes(this.searchFilter);

                    if (aContainsSearchTerm && !bContainsSearchTerm) {
                        return -1; // a debe estar antes que b
                    } else if (!aContainsSearchTerm && bContainsSearchTerm) {
                        return 1; // b debe estar antes que a
                    } else if (aContainsSearchTerm && bContainsSearchTerm) {
                        // Si ambas contienen, comparar longitud para que la más corta vaya primero
                        return a.name.length - b.name.length;
                    } else {
                        // Si ambas no contienen, ordenar por similitud
                        const aSimilarity = this.calculateSimilarity(a.name, this.searchFilter);
                        const bSimilarity = this.calculateSimilarity(b.name, this.searchFilter);
                        return bSimilarity - aSimilarity; // Ordena de forma descendente por similitud
                    }
                });
            }
          }
        }
        return filtered;
      }
    },
    methods: {
        calculateSimilarity(str1, str2) {
            // Implementa una función para calcular la similitud entre dos cadenas
            // Devuelve un valor numérico de similitud (mayor valor = más similar)
            // Aquí se usa la longitud de la cadena compartida como ejemplo
            const commonChars = Array.from(new Set(str1)).filter(char => str2.includes(char)).join('');
            return commonChars.length;
        },
        escapeRegExp(text) {
            return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&'); // Escapa caracteres especiales
        },
      selectOption(option) {
        this.selected = option;
        this.optionsShown = false;
        this.searchFilter = this.selected.name;
        if(this.change != '')
          this.selected.change = true;
        else
          this.selected.change = false;
        this.$emit('selected', this.selected);
      },
      showOptions(){
        if (!this.disabled) {
          this.searchFilter = '';
          this.optionsShown = true;
        }
      },
      Clear()
      {
        this.selected = '';
        this.optionsShown = false;
        this.searchFilter= '';
        this.$emit('selected', this.selected);
      },
      exit() {
        if (!this.selected.id) {
          this.selected = {};
          this.searchFilter = '';
        } else {
          this.searchFilter = this.selected.name;
        }
        this.$emit('selected', this.selected);
        this.optionsShown = false;
      },
      // Selecting when pressing Enter
      keyMonitor: function(event) {
        if (event.key === "Enter" && this.filteredOptions[0])
          this.selectOption(this.filteredOptions[0]);
      }
    },
    watch: {
      searchFilter() {
        if (this.filteredOptions.length === 0) {
          this.selected = {};
        } else {
          this.selected = this.filteredOptions[0];
        }
        this.$emit('filter', this.searchFilter);
      }
    }
  };
</script>


<style lang="scss" scoped>
  .dropdown {
    position: relative;
    display: block;
    margin: auto;
    .dropdown-input {
    //   background: #fff;
      cursor: pointer;
      border: 1px solid #e7ecf5;
    //   border-radius: 3px;
      color: #333;
      display: block;
    //   font-size: .8em;
    //   padding: 6px;
    //   min-width: 250px;
    //   max-width: 250px;
      // &:hover {
      //   background: #f8f8fa;
      // }

      background-color: white;
      /* inline SVG */
      background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20256%20448%22%20enable-background%3D%22new%200%200%20256%20448%22%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E.arrow%7Bfill%3A%23424242%3B%7D%3C%2Fstyle%3E%3Cpath%20class%3D%22arrow%22%20d%3D%22M255.9%20168c0-4.2-1.6-7.9-4.8-11.2-3.2-3.2-6.9-4.8-11.2-4.8H16c-4.2%200-7.9%201.6-11.2%204.8S0%20163.8%200%20168c0%204.4%201.6%208.2%204.8%2011.4l112%20112c3.1%203.1%206.8%204.6%2011.2%204.6%204.4%200%208.2-1.5%2011.4-4.6l112-112c3-3.2%204.5-7%204.5-11.4z%22%2F%3E%3C%2Fsvg%3E%0A");
      background-position: right 10px center;
      background-repeat: no-repeat;
      background-size: auto 50%;
      border-radius: 2px;
      border: none;
      padding: 10px 30px 10px 10px;
      outline: none;
      -moz-appearance: none;
      -webkit-appearance: none;
      appearance: none;

      border: 1px solid #e9e9e9;
      border-radius: 20px;

    }
    .dropdown-content {
      position: absolute;
      background-color: #fff;
    //   min-width: 248px;
      // max-width: 248px;
      max-height: 248px;
      border: 1px solid #e7ecf5;
      border-radius: 20px;
      box-shadow: 0px -8px 34px 0px rgba(0,0,0,0.05);
      overflow: auto;
      z-index: 5;
      .dropdown-item {
        color: black;
        // font-size: .7em;
        line-height: 1em;
        padding: 12px;
        text-decoration: none;
        display: block;
        cursor: pointer;
        &:hover {
          background-color: #e7ecf5;
        }
      }
    }
    .dropdown:hover .dropdowncontent {
      display: block;
    }

    .dropdown-input:hover {
      background-color: #fbfbfb
    }
    .noDiligenciado {
        box-shadow: 0 0 12px red;
    }
  }
</style>
