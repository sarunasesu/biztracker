<script setup lang="ts">
import { ref } from "vue";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable"; // ✅ Named import
import * as XLSX from "xlsx";
import axios from "../axios";

import {
  DocumentArrowDownIcon,
  DocumentTextIcon,
  CalendarIcon,
  ChartBarIcon,
} from "@heroicons/vue/24/outline";

// Report types configuration
const reportTypes = ref([
  {
    title: "Monthly Financial Report",
    description: "Complete overview of revenue, expenses, and profit",
    format: "PDF",
    icon: DocumentTextIcon,
    color: "blue",
    type: "financial",
  },
  {
    title: "Inventory Report",
    description: "Current stock levels and product details",
    format: "Excel",
    icon: DocumentArrowDownIcon,
    color: "green",
    type: "inventory",
  },
  {
    title: "Monthly Expense Breakdown",
    description: "Detailed analysis of all business expenses",
    format: "PDF",
    icon: DocumentTextIcon,
    color: "red",
    type: "expenses",
  },
  {
    title: "Revenue Analysis",
    description: "Revenue trends and performance metrics",
    format: "Excel",
    icon: DocumentArrowDownIcon,
    color: "purple",
    type: "revenue",
  },
]);

// Stats
const reportsGenerated = ref(0);
const lastReport = ref("Never");
const availableFormats = ref("PDF & Excel");

// Loading states
const loadingStates = ref<Record<string, boolean>>({});

// Custom report form data
const customReport = ref({
  startDate: "",
  endDate: "",
  includeRevenue: true,
  includeExpenses: true,
  includeProfit: true,
  includeInventory: false,
});

const filterByDate = (items: any[], start?: string, end?: string) => {
  const startDate = start ? new Date(start) : null;
  const endDate = end ? new Date(end) : null;

  return items.filter((item) => {
    const itemDate = new Date(item.date);
    return (
      (!startDate || itemDate >= startDate) && (!endDate || itemDate <= endDate)
    );
  });
};

// API fetch functions
const fetchProducts = async () => {
  try {
    const res = await axios.get("/api/products");
    return res.data;
  } catch (err) {
    console.error("Error fetching products:", err);
    return [];
  }
};

const fetchRevenue = async () => {
  try {
    const res = await axios.get("/api/revenue");
    return res.data;
  } catch (err) {
    console.error("Error fetching revenue:", err);
    return [];
  }
};

const fetchExpenses = async () => {
  try {
    const res = await axios.get("/api/expenses");
    return res.data;
  } catch (err) {
    console.error("Error fetching expenses:", err);
    return [];
  }
};

// Excel generation functions
const generateInventoryExcel = async () => {
  loadingStates.value["inventory"] = true;

  try {
    const XLSX = await import("xlsx-js-style");
    const products = await fetchProducts();
    const wb = XLSX.utils.book_new();

    const productsData = products.map((product: any) => ({
      "Product Name": product.name,
      SKU: product.sku,
      Category: product.category?.name || product.category || "N/A",
      "Cost Price": product.costPrice ?? 0,
      "Value Price": product.valuePrice ?? 0,
      "Sold Price": product.soldPrice ?? "N/A",
      "Current Stock": product.stock ?? 0,
      "Purchase Date": (product.purchaseDate || "").split("T")[0] || "N/A",
      "Sold Date": (product.soldDate || "").split("T")[0] || "N/A",
      "Total Value": (product.stock ?? 0) * (product.valuePrice ?? 0),
      Description: product.description || "N/A",
    }));

    const ws = XLSX.utils.json_to_sheet(productsData);
    ws["!cols"] = [
      { width: 25 },
      { width: 15 },
      { width: 18 },
      { width: 12 },
      { width: 12 },
      { width: 12 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 35 },
    ];

    const headerRange = XLSX.utils.decode_range(ws["!ref"] || "A1");

    for (let col = headerRange.s.c; col <= headerRange.e.c; col++) {
      const cell = XLSX.utils.encode_cell({ r: 0, c: col });
      if (!ws[cell]) continue;
      ws[cell].s = {
        font: { bold: true, color: { rgb: "FFFFFF" } },
        fill: { fgColor: { rgb: "2563EB" } },
        alignment: { horizontal: "center", vertical: "center" },
        border: {
          top: { style: "thin", color: { rgb: "000000" } },
          bottom: { style: "thin", color: { rgb: "000000" } },
          left: { style: "thin", color: { rgb: "000000" } },
          right: { style: "thin", color: { rgb: "000000" } },
        },
      };
    }

    for (let row = 1; row <= headerRange.e.r; row++) {
      const isEven = row % 2 === 0;
      for (let col = headerRange.s.c; col <= headerRange.e.c; col++) {
        const cell = XLSX.utils.encode_cell({ r: row, c: col });
        if (!ws[cell]) continue;

        if (col === 6) {
          ws[cell].z = "#,##0";
          const val = ws[cell].v;
          if (val < 20) {
            ws[cell].s = {
              font: { bold: true, color: { rgb: "DC2626" } },
              fill: { fgColor: { rgb: "FEE2E2" } },
              border: {
                top: { style: "thin", color: { rgb: "E5E7EB" } },
                bottom: { style: "thin", color: { rgb: "E5E7EB" } },
                left: { style: "thin", color: { rgb: "E5E7EB" } },
                right: { style: "thin", color: { rgb: "E5E7EB" } },
              },
            };
            continue;
          }
        }

        if (!ws[cell].s) ws[cell].s = {};
        ws[cell].s.fill = {
          fgColor: { rgb: isEven ? "F9FAFB" : "FFFFFF" },
        };
        ws[cell].s.border = {
          top: { style: "thin", color: { rgb: "E5E7EB" } },
          bottom: { style: "thin", color: { rgb: "E5E7EB" } },
          left: { style: "thin", color: { rgb: "E5E7EB" } },
          right: { style: "thin", color: { rgb: "E5E7EB" } },
        };

        if ([1, 6, 7, 8].includes(col)) {
          ws[cell].s.alignment = {
            horizontal: "center",
            vertical: "center",
          };
        }

        if ([3, 4, 9].includes(col)) {
          ws[cell].z = '"$"#,##0.00';
        }
      }
    }

    XLSX.utils.book_append_sheet(wb, ws, "Inventory");

    // --- Summary ---
    const totalProducts = products.length;
    const totalValue = products.reduce(
      (sum, p) => sum + (p.stock ?? 0) * (p.valuePrice ?? 0),
      0
    );
    const lowStockItems = products.filter((p) => (p.stock ?? 0) < 20).length;

    const summaryData = [
      { Metric: "Total Products", Value: totalProducts },
      {
        Metric: "Total Inventory Value",
        Value: `$${totalValue.toLocaleString()}`,
      },
      { Metric: "Low Stock Items (< 20)", Value: lowStockItems },
      {
        Metric: "Average Product Value",
        Value: `$${(totalValue / totalProducts || 0).toFixed(2)}`,
      },
      {
        Metric: "Stock Health",
        Value: lowStockItems > 0 ? "Attention Needed" : "Good",
      },
      { Metric: "Report Generated", Value: new Date().toLocaleDateString() },
      { Metric: "Generated By", Value: "BizTracker System" },
    ];

    const summaryWS = XLSX.utils.json_to_sheet(summaryData);
    summaryWS["!cols"] = [{ width: 30 }, { width: 30 }];
    const summaryRange = XLSX.utils.decode_range(summaryWS["!ref"] || "A1");

    for (let row = 0; row <= summaryRange.e.r; row++) {
      for (let col = 0; col <= summaryRange.e.c; col++) {
        const cell = XLSX.utils.encode_cell({ r: row, c: col });
        if (!summaryWS[cell]) continue;
        const value = summaryWS[cell].v;

        summaryWS[cell].s = {
          font: {
            bold: col === 0,
            color: { rgb: col === 0 ? "374151" : "1F2937" },
          },
          alignment: {
            horizontal: col === 0 ? "left" : "right",
            vertical: "center",
          },
          fill: {
            fgColor: { rgb: row % 2 === 0 ? "F9FAFB" : "FFFFFF" },
          },
          border: {
            top: { style: "thin", color: { rgb: "E5E7EB" } },
            bottom: { style: "thin", color: { rgb: "E5E7EB" } },
            left: { style: "thin", color: { rgb: "E5E7EB" } },
            right: { style: "thin", color: { rgb: "E5E7EB" } },
          },
        };
      }
    }

    XLSX.utils.book_append_sheet(wb, summaryWS, "Summary");

    XLSX.writeFile(
      wb,
      `inventory-report-${new Date().toISOString().split("T")[0]}.xlsx`
    );
    reportsGenerated.value++;
    lastReport.value = new Date().toLocaleDateString();
  } catch (error) {
    console.error("Error generating inventory report:", error);
    alert("Error generating inventory report. Please try again.");
  } finally {
    loadingStates.value["inventory"] = false;
  }
};

const generateRevenueExcel = async () => {
  const XLSX = await import("xlsx-js-style");
  const revenue = await fetchRevenue();

  const wb = XLSX.utils.book_new();

  const revenueData = revenue.map((item: any) => ({
    Description: item.description,
    Amount: item.amount,
    Category: item.category,
    Date: (item.date || "").split("T")[0] || "N/A",
    "Payment Method": item.paymentMethod || "N/A",
    Customer: item.customer || "N/A",
    "Invoice Number": item.invoiceNumber || "N/A",
    Notes: item.notes || "",
  }));

  const ws = XLSX.utils.json_to_sheet(revenueData);

  ws["!cols"] = [
    { width: 30 }, // Description
    { width: 15 }, // Amount
    { width: 15 }, // Category
    { width: 15 }, // Date
    { width: 15 }, // Payment Method
    { width: 20 }, // Customer
    { width: 20 }, // Invoice Number
    { width: 30 }, // Notes
  ];

  const range = XLSX.utils.decode_range(ws["!ref"] || "A1");

  for (let col = range.s.c; col <= range.e.c; col++) {
    const cell = XLSX.utils.encode_cell({ r: 0, c: col });
    if (!ws[cell]) continue;
    ws[cell].s = {
      font: { bold: true, color: { rgb: "FFFFFF" } },
      fill: { fgColor: { rgb: "2563EB" } },
      alignment: { horizontal: "center", vertical: "center" },
      border: {
        top: { style: "thin", color: { rgb: "000000" } },
        bottom: { style: "thin", color: { rgb: "000000" } },
        left: { style: "thin", color: { rgb: "000000" } },
        right: { style: "thin", color: { rgb: "000000" } },
      },
    };
  }

  for (let row = 1; row <= range.e.r; row++) {
    const isEven = row % 2 === 0;
    for (let col = range.s.c; col <= range.e.c; col++) {
      const cell = XLSX.utils.encode_cell({ r: row, c: col });
      if (!ws[cell]) continue;

      if (!ws[cell].s) ws[cell].s = {};
      ws[cell].s.fill = {
        fgColor: { rgb: isEven ? "F9FAFB" : "FFFFFF" },
      };
      ws[cell].s.border = {
        top: { style: "thin", color: { rgb: "E5E7EB" } },
        bottom: { style: "thin", color: { rgb: "E5E7EB" } },
        left: { style: "thin", color: { rgb: "E5E7EB" } },
        right: { style: "thin", color: { rgb: "E5E7EB" } },
      };

      if (col === 1) {
        ws[cell].z = '"$"#,##0.00';
        ws[cell].s.alignment = { horizontal: "right", vertical: "center" };
      } else if (col === 3) {
        ws[cell].s.alignment = { horizontal: "center", vertical: "center" };
      }
    }
  }

  XLSX.utils.book_append_sheet(wb, ws, "Revenue");

  // === Summary Sheet ===
  const totalRevenue = revenue.reduce(
    (sum: number, r: any) => sum + r.amount,
    0
  );

  const summaryData = [
    { Metric: "Total Revenue", Value: `$${totalRevenue.toLocaleString()}` },
    { Metric: "Total Entries", Value: revenue.length },
    {
      Metric: "Average Revenue",
      Value: `$${(totalRevenue / revenue.length || 0).toFixed(2)}`,
    },
    { Metric: "Report Generated", Value: new Date().toLocaleDateString() },
    { Metric: "Generated By", Value: "BizTracker System" },
  ];

  const summaryWS = XLSX.utils.json_to_sheet(summaryData);
  summaryWS["!cols"] = [{ width: 30 }, { width: 30 }];
  const summaryRange = XLSX.utils.decode_range(summaryWS["!ref"] || "A1");

  for (let row = 0; row <= summaryRange.e.r; row++) {
    for (let col = 0; col <= summaryRange.e.c; col++) {
      const cell = XLSX.utils.encode_cell({ r: row, c: col });
      if (!summaryWS[cell]) continue;

      summaryWS[cell].s = {
        font: {
          bold: col === 0,
          color: { rgb: col === 0 ? "374151" : "1F2937" },
        },
        alignment: {
          horizontal: col === 0 ? "left" : "right",
          vertical: "center",
        },
        fill: {
          fgColor: { rgb: row % 2 === 0 ? "F9FAFB" : "FFFFFF" },
        },
        border: {
          top: { style: "thin", color: { rgb: "E5E7EB" } },
          bottom: { style: "thin", color: { rgb: "E5E7EB" } },
          left: { style: "thin", color: { rgb: "E5E7EB" } },
          right: { style: "thin", color: { rgb: "E5E7EB" } },
        },
      };

      const value = summaryWS[cell].v;
      if (col === 1 && typeof value === "string" && value.startsWith("$")) {
        summaryWS[cell].s.font = {
          bold: true,
          color: { rgb: "059669" },
        };
      }
    }
  }
  reportsGenerated.value++;
  lastReport.value = new Date().toLocaleDateString();

  XLSX.utils.book_append_sheet(wb, summaryWS, "Summary");

  XLSX.writeFile(
    wb,
    `revenue-report-${new Date().toISOString().split("T")[0]}.xlsx`
  );
};

const generateFinancialPDF = async () => {
  loadingStates.value["financial"] = true;

  try {
    const [revenue, expenses] = await Promise.all([
      fetchRevenue(),
      fetchExpenses(),
    ]);

    const doc = new jsPDF();

    // Header
    doc.setFontSize(20);
    doc.setTextColor(59, 130, 246); // Blue
    doc.text("Monthly Financial Report", 20, 20);

    doc.setFontSize(12);
    doc.setTextColor(0, 0, 0);
    doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 20, 30);

    // Summary
    const totalRevenue = revenue.reduce(
      (sum: number, r: any) => sum + (r.amount || 0),
      0
    );
    const totalExpenses = expenses.reduce(
      (sum: number, e: any) => sum + (e.amount || 0),
      0
    );
    const netProfit = totalRevenue - totalExpenses;
    const margin = totalRevenue
      ? ((netProfit / totalRevenue) * 100).toFixed(2)
      : "0.00";

    doc.setFontSize(16);
    doc.text("Financial Summary", 20, 50);

    doc.setFontSize(12);
    doc.text(`Total Revenue: $${totalRevenue.toLocaleString()}`, 20, 65);
    doc.text(`Total Expenses: $${totalExpenses.toLocaleString()}`, 20, 75);
    doc.text(`Net Profit: $${netProfit.toLocaleString()}`, 20, 85);
    doc.text(`Profit Margin: ${margin}%`, 20, 95);

    // Revenue Table
    if (revenue.length > 0) {
      doc.setFontSize(14);
      doc.text("Revenue Details", 20, 115);

      autoTable(doc, {
        head: [["Description", "Amount", "Category", "Date"]],
        body: revenue.map((item: any) => [
          item.description || "",
          `$${(item.amount || 0).toLocaleString()}`,
          item.category?.name || item.category || "N/A",
          (item.date || "").split("T")[0] || "N/A",
        ]),
        startY: 125,
        styles: { fontSize: 9 },
        headStyles: { fillColor: [59, 130, 246] },
      });
    }

    // Expenses Table
    if (expenses.length > 0) {
      const finalY = (doc as any).lastAutoTable?.finalY || 180;
      doc.setFontSize(14);
      doc.text("Expense Details", 20, finalY + 20);

      autoTable(doc, {
        head: [["Description", "Amount", "Category", "Date"]],
        body: expenses.map((item: any) => [
          item.description || "",
          `$${(item.amount || 0).toLocaleString()}`,
          item.category?.name || item.category || "N/A",
          (item.date || "").split("T")[0] || "N/A",
        ]),
        startY: finalY + 30,
        styles: { fontSize: 9 },
        headStyles: { fillColor: [239, 68, 68] },
      });
    }

    doc.save(`financial-report-${new Date().toISOString().split("T")[0]}.pdf`);
    reportsGenerated.value++;
    lastReport.value = new Date().toLocaleDateString();
  } catch (error) {
    console.error("Error generating financial PDF report:", error);
    alert("Error generating financial PDF report. Please try again.");
  } finally {
    loadingStates.value["financial"] = false;
  }
};

const generateExpensesPDF = async () => {
  loadingStates.value["expenses"] = true;

  try {
    const expenses = await fetchExpenses();

    // Filter by current month
    const now = new Date();
    const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
    const monthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    const filteredExpenses = filterByDate(
      expenses,
      monthStart.toISOString(),
      monthEnd.toISOString()
    );

    const doc = new jsPDF();

    // Header
    doc.setFontSize(20);
    doc.setTextColor(239, 68, 68); // Red
    doc.text("Expense Breakdown Report", 20, 20);

    doc.setFontSize(12);
    doc.setTextColor(0, 0, 0);
    doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 20, 30);

    // Summary
    const totalExpenses = filteredExpenses.reduce(
      (sum: number, e: any) => sum + (e.amount || 0),
      0
    );
    const avgExpense = totalExpenses / (filteredExpenses.length || 1);

    doc.setFontSize(16);
    doc.text("Expense Summary", 20, 50);

    doc.setFontSize(12);
    doc.text(`Total Expenses: $${totalExpenses.toLocaleString()}`, 20, 65);
    doc.text(`Total Entries: ${filteredExpenses.length}`, 20, 75);
    doc.text(`Average Expense: $${avgExpense.toFixed(2)}`, 20, 85);

    // Expenses Table
    if (filteredExpenses.length > 0) {
      autoTable(doc, {
        head: [["Description", "Amount", "Category", "Date", "Vendor"]],
        body: filteredExpenses.map((item: any) => [
          item.description || "",
          `$${(item.amount || 0).toLocaleString()}`,
          item.category?.name || item.category || "N/A",
          (item.date || "").split("T")[0] || "N/A",
          item.vendor || "N/A",
        ]),
        startY: 100,
        styles: { fontSize: 9 },
        headStyles: { fillColor: [239, 68, 68] },
      });
    }

    doc.save(`expenses-report-${new Date().toISOString().split("T")[0]}.pdf`);
    reportsGenerated.value++;
    lastReport.value = new Date().toLocaleDateString();
  } catch (error) {
    console.error("Error generating expenses PDF report:", error);
    alert("Error generating expenses PDF report. Please try again.");
  } finally {
    loadingStates.value["expenses"] = false;
  }
};

// Main report generation function
const generateReport = async (reportTitle: string, type: string) => {
  loadingStates.value[type] = true;

  try {
    switch (type) {
      case "financial":
        await generateFinancialPDF();
        break;
      case "inventory":
        await generateInventoryExcel();
        break;
      case "expenses":
        await generateExpensesPDF();
        break;
      case "revenue":
        await generateRevenueExcel();
        break;
      default:
        console.error("Unknown report type:", type);
    }

    // Update stats
    lastReport.value = new Date().toLocaleDateString();
  } catch (error) {
    console.error("Error generating report:", error);
    alert("Error generating report. Please try again.");
  } finally {
    loadingStates.value[type] = false;
  }
};

// Custom report generation
const generateCustomPDF = async () => {
  loadingStates.value["custom-pdf"] = true;

  try {
    const [revenueRaw, expensesRaw, inventoryRaw] = await Promise.all([
      fetchRevenue(),
      fetchExpenses(),
      fetchProducts(), // <-- Make sure this exists
    ]);

    const revenue = customReport.value.includeRevenue
      ? filterByDate(
          revenueRaw,
          customReport.value.startDate,
          customReport.value.endDate
        )
      : [];

    const expenses = customReport.value.includeExpenses
      ? filterByDate(
          expensesRaw,
          customReport.value.startDate,
          customReport.value.endDate
        )
      : [];

    const inventory = customReport.value.includeInventory ? inventoryRaw : [];

    const doc = new jsPDF();

    // Header
    doc.setFontSize(20);
    doc.setTextColor(59, 130, 246); // Blue
    doc.text("Custom Business Report", 20, 20);

    doc.setFontSize(12);
    doc.setTextColor(0, 0, 0);
    doc.text(
      `Period: ${customReport.value.startDate || "All time"} to ${
        customReport.value.endDate || "Present"
      }`,
      20,
      30
    );
    doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 20, 40);

    let currentY = 55;

    // Revenue Table
    if (customReport.value.includeRevenue && revenue.length > 0) {
      const totalRevenue = revenue.reduce((sum, r) => sum + (r.amount || 0), 0);

      doc.setFontSize(16);
      doc.text("Revenue Summary", 20, currentY);
      currentY += 10;

      doc.setFontSize(12);
      doc.text(
        `Total Revenue: $${totalRevenue.toLocaleString()}`,
        20,
        currentY + 5
      );
      currentY += 15;

      autoTable(doc, {
        startY: currentY,
        head: [["Description", "Amount", "Category", "Date"]],
        body: revenue.map((item: any) => [
          item.description,
          `$${(item.amount || 0).toLocaleString()}`,
          item.category?.name || item.category || "N/A",
          (item.date || "").split("T")[0],
        ]),
        styles: { fontSize: 9 },
        headStyles: { fillColor: [59, 130, 246] },
      });

      currentY = (doc as any).lastAutoTable.finalY + 15;
    }

    // Expenses Table
    if (customReport.value.includeExpenses && expenses.length > 0) {
      const totalExpenses = expenses.reduce(
        (sum, e) => sum + (e.amount || 0),
        0
      );

      doc.setFontSize(16);
      doc.text("Expenses Summary", 20, currentY);
      currentY += 10;

      doc.setFontSize(12);
      doc.text(
        `Total Expenses: $${totalExpenses.toLocaleString()}`,
        20,
        currentY + 5
      );
      currentY += 15;

      autoTable(doc, {
        startY: currentY,
        head: [["Description", "Amount", "Category", "Date"]],
        body: expenses.map((item: any) => [
          item.description,
          `$${(item.amount || 0).toLocaleString()}`,
          item.category?.name || item.category || "N/A",
          (item.date || "").split("T")[0],
        ]),
        styles: { fontSize: 9 },
        headStyles: { fillColor: [239, 68, 68] },
      });

      currentY = (doc as any).lastAutoTable.finalY + 15;
    }

    // Inventory Table
    if (customReport.value.includeInventory && inventory.length > 0) {
      doc.setFontSize(16);
      doc.text("Inventory Summary", 20, currentY);
      currentY += 10;

      autoTable(doc, {
        startY: currentY,
        head: [["Product Name", "Price", "Stock", "Category", "SKU"]],
        body: inventory.map((item: any) => [
          item.name || "Unnamed",
          item.valuePrice ? `$${item.valuePrice.toFixed(2)}` : "N/A",
          item.stock ?? "N/A",
          item.category.name ?? "N/A",
          item.sku ?? "N/A",
        ]),
        styles: { fontSize: 9 },
        headStyles: { fillColor: [34, 197, 94] }, // Green
      });

      currentY = (doc as any).lastAutoTable.finalY + 15;
    }

    // Profit Summary
    if (customReport.value.includeProfit && revenue.length && expenses.length) {
      const totalRevenue = revenue.reduce((sum, r) => sum + (r.amount || 0), 0);
      const totalExpenses = expenses.reduce(
        (sum, e) => sum + (e.amount || 0),
        0
      );
      const netProfit = totalRevenue - totalExpenses;
      const margin = totalRevenue
        ? ((netProfit / totalRevenue) * 100).toFixed(2)
        : "0.00";

      doc.setFontSize(16);
      doc.text("Profit Summary", 20, currentY);
      currentY += 10;

      doc.setFontSize(12);
      doc.text(`Net Profit: $${netProfit.toLocaleString()}`, 20, currentY + 5);
      doc.text(`Profit Margin: ${margin}%`, 20, currentY + 15);
    }

    doc.save(`custom-report-${new Date().toISOString().split("T")[0]}.pdf`);
    reportsGenerated.value++;
    lastReport.value = new Date().toLocaleDateString();
  } catch (error) {
    console.error("Error generating custom PDF:", error);
    alert("Error generating custom PDF report. Please try again.");
  } finally {
    loadingStates.value["custom-pdf"] = false;
  }
};

const generateCustomExcel = async () => {
  loadingStates.value["custom-excel"] = true;

  try {
    const XLSX = await import("xlsx-js-style");
    const wb = XLSX.utils.book_new();

    const [revenueRaw, expensesRaw, products] = await Promise.all([
      fetchRevenue(),
      fetchExpenses(),
      fetchProducts(),
    ]);

    const startDate = customReport.value.startDate;
    const endDate = customReport.value.endDate;

    const revenue = customReport.value.includeRevenue
      ? filterByDate(revenueRaw, startDate, endDate)
      : [];

    const expenses = customReport.value.includeExpenses
      ? filterByDate(expensesRaw, startDate, endDate)
      : [];

    const inventory = customReport.value.includeInventory ? products : [];

    const styleHeader = {
      font: { bold: true, color: { rgb: "FFFFFF" } },
      alignment: { horizontal: "center", vertical: "center" },
      border: {
        top: { style: "thin", color: { rgb: "000000" } },
        bottom: { style: "thin", color: { rgb: "000000" } },
        left: { style: "thin", color: { rgb: "000000" } },
        right: { style: "thin", color: { rgb: "000000" } },
      },
    };

    const zebraStyle = (row) => ({
      fill: { fgColor: { rgb: row % 2 === 0 ? "F9FAFB" : "FFFFFF" } },
      border: {
        top: { style: "thin", color: { rgb: "E5E7EB" } },
        bottom: { style: "thin", color: { rgb: "E5E7EB" } },
        left: { style: "thin", color: { rgb: "E5E7EB" } },
        right: { style: "thin", color: { rgb: "E5E7EB" } },
      },
    });

    if (revenue.length > 0) {
      const data = revenue.map((item) => ({
        Description: item.description,
        Amount: item.amount || 0,
        Category: item.category?.name || item.category || "N/A",
        Date: (item.date || "").split("T")[0],
        Customer: item.customer || "N/A",
        "Payment Method": item.paymentMethod || "N/A",
        Notes: item.notes || "",
        "Invoice #": item.invoiceNumber || "N/A",
      }));

      const ws = XLSX.utils.json_to_sheet(data);
      ws["!cols"] = [
        { width: 30 },
        { width: 15 },
        { width: 15 },
        { width: 15 },
        { width: 20 },
        { width: 20 },
        { width: 30 },
        { width: 20 },
      ];

      const range = XLSX.utils.decode_range(ws["!ref"]);
      for (let col = range.s.c; col <= range.e.c; col++) {
        const cell = XLSX.utils.encode_cell({ r: 0, c: col });
        if (ws[cell]) {
          ws[cell].s = { ...styleHeader, fill: { fgColor: { rgb: "2563EB" } } };
        }
      }

      for (let row = 1; row <= range.e.r; row++) {
        for (let col = range.s.c; col <= range.e.c; col++) {
          const cell = XLSX.utils.encode_cell({ r: row, c: col });
          if (!ws[cell]) continue;
          ws[cell].s = zebraStyle(row);
          if (col === 1) {
            ws[cell].z = '"$"#,##0.00';
            ws[cell].s.alignment = { horizontal: "right", vertical: "center" };
          }
        }
      }

      XLSX.utils.book_append_sheet(wb, ws, "Revenue");
    }

    if (expenses.length > 0) {
      const data = expenses.map((item) => ({
        Description: item.description,
        Amount: item.amount || 0,
        Category: item.category?.name || item.category || "N/A",
        Date: (item.date || "").split("T")[0],
        Vendor: item.vendor || "N/A",
        "Payment Method": item.paymentMethod || "N/A",
        Notes: item.notes || "",
        "Receipt #": item.receiptNumber || "N/A",
      }));

      const ws = XLSX.utils.json_to_sheet(data);
      ws["!cols"] = [
        { width: 30 },
        { width: 15 },
        { width: 15 },
        { width: 15 },
        { width: 20 },
        { width: 20 },
        { width: 30 },
        { width: 20 },
      ];

      const range = XLSX.utils.decode_range(ws["!ref"]);
      for (let col = range.s.c; col <= range.e.c; col++) {
        const cell = XLSX.utils.encode_cell({ r: 0, c: col });
        if (ws[cell]) {
          ws[cell].s = { ...styleHeader, fill: { fgColor: { rgb: "EF4444" } } };
        }
      }

      for (let row = 1; row <= range.e.r; row++) {
        for (let col = range.s.c; col <= range.e.c; col++) {
          const cell = XLSX.utils.encode_cell({ r: row, c: col });
          if (!ws[cell]) continue;
          ws[cell].s = zebraStyle(row);
          if (col === 1) {
            ws[cell].z = '"$"#,##0.00';
            ws[cell].s.alignment = { horizontal: "right", vertical: "center" };
          }
        }
      }

      XLSX.utils.book_append_sheet(wb, ws, "Expenses");
    }

    if (inventory.length > 0) {
      const data = inventory.map((product) => ({
        "Product Name": product.name,
        SKU: product.sku,
        Category: product.category?.name || product.category || "N/A",
        "Current Stock": product.stock ?? 0,
        "Value Price": product.valuePrice ?? 0,
        "Total Value": (product.stock ?? 0) * (product.valuePrice ?? 0),
        "Sold Price": product.soldPrice ?? "N/A",
        "Purchase Date": product.purchaseDate
          ? product.purchaseDate.split("T")[0]
          : "N/A",
        "Sold Date": product.soldDate ? product.soldDate.split("T")[0] : "N/A",
        Description: product.description || "",
      }));

      const ws = XLSX.utils.json_to_sheet(data);
      ws["!cols"] = Array.from({ length: 10 }, () => ({ width: 20 }));
      const range = XLSX.utils.decode_range(ws["!ref"]);

      for (let col = range.s.c; col <= range.e.c; col++) {
        const cell = XLSX.utils.encode_cell({ r: 0, c: col });
        if (ws[cell]) {
          ws[cell].s = { ...styleHeader, fill: { fgColor: { rgb: "22C55E" } } };
        }
      }

      for (let row = 1; row <= range.e.r; row++) {
        for (let col = range.s.c; col <= range.e.c; col++) {
          const cell = XLSX.utils.encode_cell({ r: row, c: col });
          if (!ws[cell]) continue;
          ws[cell].s = zebraStyle(row);
          if (col === 4 || col === 5) {
            ws[cell].z = '"$"#,##0.00';
            ws[cell].s.alignment = { horizontal: "right", vertical: "center" };
          }
        }
      }

      XLSX.utils.book_append_sheet(wb, ws, "Inventory");
    }

    // === Summary Sheet ===
    const totalRevenue = revenue.reduce((sum, r) => sum + (r.amount || 0), 0);
    const totalExpenses = expenses.reduce((sum, e) => sum + (e.amount || 0), 0);
    const netProfit = totalRevenue - totalExpenses;

    const summaryData = [
      { Metric: "Total Revenue", Value: `$${totalRevenue.toLocaleString()}` },
      { Metric: "Total Expenses", Value: `$${totalExpenses.toLocaleString()}` },
      { Metric: "Net Profit", Value: `$${netProfit.toLocaleString()}` },
      { Metric: "Entries: Revenue", Value: revenue.length },
      { Metric: "Entries: Expenses", Value: expenses.length },
      { Metric: "Entries: Inventory", Value: inventory.length },
      { Metric: "Report Generated", Value: new Date().toLocaleDateString() },
      { Metric: "Generated By", Value: "BizTracker System" },
    ];

    const summaryWS = XLSX.utils.json_to_sheet(summaryData);
    summaryWS["!cols"] = [{ width: 30 }, { width: 30 }];
    const range = XLSX.utils.decode_range(summaryWS["!ref"]);

    for (let row = 0; row <= range.e.r; row++) {
      for (let col = 0; col <= range.e.c; col++) {
        const cell = XLSX.utils.encode_cell({ r: row, c: col });
        if (!summaryWS[cell]) continue;

        summaryWS[cell].s = {
          font: {
            bold: col === 0,
            color: { rgb: col === 0 ? "374151" : "1F2937" },
          },
          alignment: {
            horizontal: col === 0 ? "left" : "right",
            vertical: "center",
          },
          fill: {
            fgColor: { rgb: row % 2 === 0 ? "F9FAFB" : "FFFFFF" },
          },
          border: {
            top: { style: "thin", color: { rgb: "E5E7EB" } },
            bottom: { style: "thin", color: { rgb: "E5E7EB" } },
            left: { style: "thin", color: { rgb: "E5E7EB" } },
            right: { style: "thin", color: { rgb: "E5E7EB" } },
          },
        };

        if (
          col === 1 &&
          typeof summaryWS[cell].v === "string" &&
          summaryWS[cell].v.startsWith("$")
        ) {
          summaryWS[cell].s.font = {
            bold: true,
            color: { rgb: "059669" },
          };
        }
      }
    }

    XLSX.utils.book_append_sheet(wb, summaryWS, "Summary");
    XLSX.writeFile(
      wb,
      `custom-report-${new Date().toISOString().split("T")[0]}.xlsx`
    );
    reportsGenerated.value++;
    lastReport.value = new Date().toLocaleDateString();
  } catch (error) {
    console.error("Error generating custom Excel report:", error);
    alert("Error generating custom Excel report. Please try again.");
  } finally {
    loadingStates.value["custom-excel"] = false;
  }
};

const getIconColor = (color: string) => {
  const colors = {
    blue: "icon-blue",
    green: "icon-green",
    red: "icon-red",
    purple: "icon-purple",
  };
  return colors[color] || "icon-blue";
};
</script>

<template>
  <div class="reports-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>Reports</h1>
        <p>Generate and download business reports</p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <ChartBarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Reports Generated</span>
          <span class="stat-value">{{ reportsGenerated }}</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon purple">
          <CalendarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Last Report</span>
          <span class="stat-value">{{ lastReport }}</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon green">
          <DocumentTextIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Available Formats</span>
          <span class="stat-value">{{ availableFormats }}</span>
        </div>
      </div>
    </div>

    <!-- Report Types -->
    <div class="reports-section">
      <div class="section-header">
        <h3>Available Reports</h3>
        <span class="reports-count">{{ reportTypes.length }} report types</span>
      </div>

      <div class="reports-grid">
        <div
          v-for="report in reportTypes"
          :key="report.title"
          class="report-card"
        >
          <div class="report-header">
            <div :class="['report-icon', getIconColor(report.color)]">
              <component :is="report.icon" />
            </div>
            <span class="report-format">{{ report.format }}</span>
          </div>

          <div class="report-content">
            <h4 class="report-title">{{ report.title }}</h4>
            <p class="report-description">{{ report.description }}</p>
          </div>

          <div class="report-actions">
            <button
              @click="generateReport(report.title, report.type)"
              class="generate-btn"
              :disabled="loadingStates[report.type]"
            >
              <span v-if="!loadingStates[report.type]">Generate Report</span>
              <span v-else class="loading-text">
                <svg
                  class="animate-spin w-4 h-4 mr-2"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                Generating...
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Custom Report Builder -->
    <div class="custom-report-section">
      <div class="section-header">
        <h3>Custom Report Builder</h3>
        <span class="custom-count">Build your own</span>
      </div>

      <div class="custom-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Start Date</label>
            <input
              v-model="customReport.startDate"
              type="date"
              class="form-input"
            />
          </div>
          <div class="form-group">
            <label class="form-label">End Date</label>
            <input
              v-model="customReport.endDate"
              type="date"
              class="form-input"
            />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Include Metrics</label>
          <div class="checkbox-grid">
            <label class="checkbox-item">
              <input v-model="customReport.includeRevenue" type="checkbox" />
              <span>Revenue</span>
            </label>
            <label class="checkbox-item">
              <input v-model="customReport.includeExpenses" type="checkbox" />
              <span>Expenses</span>
            </label>
            <label class="checkbox-item">
              <input v-model="customReport.includeProfit" type="checkbox" />
              <span>Profit</span>
            </label>
            <label class="checkbox-item">
              <input v-model="customReport.includeInventory" type="checkbox" />
              <span>Inventory</span>
            </label>
          </div>
        </div>

        <div class="form-actions">
          <button
            @click="generateCustomPDF"
            class="btn-primary"
            :disabled="loadingStates['custom-pdf']"
          >
            <DocumentTextIcon class="btn-icon" />
            <span v-if="!loadingStates['custom-pdf']">Generate PDF</span>
            <span v-else>Generating...</span>
          </button>
          <button
            @click="generateCustomExcel"
            class="btn-secondary"
            :disabled="loadingStates['custom-excel']"
          >
            <DocumentArrowDownIcon class="btn-icon" />
            <span v-if="!loadingStates['custom-excel']">Generate Excel</span>
            <span v-else>Generating...</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.loading-text {
  display: flex;
  align-items: center;
  justify-content: center;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.generate-btn:disabled,
.btn-primary:disabled,
.btn-secondary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.generate-btn:disabled:hover,
.btn-primary:disabled:hover,
.btn-secondary:disabled:hover {
  transform: none;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}
</style>
