import axios from "@/axios";
import { reactive, ref } from "vue";
export function getProducts() {
  const products = ref([]);
  const isLoading = ref(false);
  const productDetails = ref(null);
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

  const fetchProductDetailsById = async (id) => {
    isLoading.value = true;
    try {
      const response = await axios.get("/api/products/show/" + id);
      console.log(response);
      if (response.status === 200) {
        productDetails.value = response.data;
      }
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };

  return {
    products,
    isLoading,
    fetchProducts,
    fetchProductsBySupplier,
    fetchProductDetailsById,
    productDetails,
  };
}

export function manageProducts() {
  const errors = ref(null);
  const addProduct = async (productCred) => {
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
      return response;
    } catch (error) {
      console.log(error);
      if (error.response.data.errors) {
        errors.value = error.response.data.errors;
      }
    }
  };
  const updateProduct = async (productCred, newPhotos) => {
    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("product_id", productCred.encrypted_id);
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
    if (newPhotos && newPhotos.length > 0) {
      newPhotos.forEach((file) => {
        formData.append("photos[]", file);
      });
    }
    try {
      const response = await axios.post("/api/products/update", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
      if (error.response.data.errors) {
        errors.value = error.response.data.errors;
      }
    }
  };

  const deletePhoto = async (id) => {
    try {
      const response = await axios.delete("/api/products/deletePhoto", {
        data: {
          photo_id: id,
        },
      });
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
    }
  };

  const deleteProduct = async (id) => {
    try {
      const response = await axios.delete("/api/products/softDelete", {
        data: {
          product_id: id,
        },
      });
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
    }
  };
  return {
    addProduct,
    errors,
    deletePhoto,
    errors,
    updateProduct,
    deleteProduct,
  };
}
