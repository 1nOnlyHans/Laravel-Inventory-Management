import axios from "@/axios";
import { reactive, ref } from "vue";
export function getProducts() {
  const products = ref([]);
  const isLoading = ref(false);

  const fetchProducts = async () => {
    isLoading.value = true;
    try {
      const response = await axios.get("/api/products/index", {
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      if (response.status === 200) {
        products.value = response.data;
      }
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };

  const fetchProductsBySupplier = async (supplier_id) => {
    try {
      const response = await axios.post(
        "/api/products/productSupplier",
        {
          supplier_id: supplier_id,
        },
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      );
      console.log(response);
      if (response.status === 200) {
        products.value = response.data;
      }
    } catch (error) {
      console.log(error);
    }
  };
  return { products, isLoading, fetchProducts, fetchProductsBySupplier };
}

export function manageProducts() {
  // const productCred = reactive({
  //   supplier_id: "",
  //   category_id: "",
  //   brand_id: "",
  //   SKU: "",
  //   model: "",
  //   product_name: "",
  //   product_description: "",
  //   product_quantity: "",
  //   unit_price: 0,
  //   reorder_level: "",
  //   status: "",
  //   photos: [],
  // });
  const addProduct = async (productCred) => {
    console.log(productCred.photos);
    const formData = new FormData();
    formData.append("supplier_id", productCred.supplier_id);
    formData.append("category_id", productCred.category_id);
    formData.append("brand_id", productCred.brand_id);
    formData.append("SKU", productCred.SKU);
    formData.append("model", productCred.model);
    formData.append("product_name", productCred.product_name);
    formData.append("product_description", productCred.product_description);
    formData.append("product_quantity", productCred.product_quantity);
    formData.append("unit_price", productCred.unit_price);
    formData.append("reorder_level", productCred.reorder_level);
    productCred.photos.forEach((file) => {
      formData.append("photos[]", file);
    });


    try {
      const response = await axios.post("/api/products/store", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      console.log(response);
    } catch (error) {
      console.log(error);
    }
  };

  return { addProduct };
}
