<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Báo Cáo Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/react@18.2.0/umd/react.production.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/react-dom@18.2.0/umd/react-dom.production.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@babel/standalone@7.20.15/babel.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <div id="root"></div>
  <script type="text/babel">
    function ReportModal({ isOpen, onClose, reportType, onSubmit }) {
      if (!isOpen) return null;

      return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 className="text-2xl font-bold mb-4">
              {reportType === 'order' && 'Lập Báo Cáo Đơn Hàng'}
            </h2>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700">Thời gian bắt đầu</label>
                <input type="date" className="mt-1 block w-full border border-gray-300 rounded-md p-2" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Thời gian kết thúc</label>
                <input type="date" className="mt-1 block w-full border border-gray-300 rounded-md p-2" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Trạng thái đơn hàng</label>
                <select className="mt-1 block w-full border border-gray-300 rounded-md p-2">
                  <option>Tất cả</option>
                  <option>Đã giao</option>
                  <option>Đang xử lý</option>
                  <option>Đã hủy</option>
                </select>
              </div>
            </div>
            <div className="mt-6 flex justify-end space-x-4">
              <button
                onClick={onClose}
                className="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
              >
                Hủy
              </button>
              <button
                onClick={() => onSubmit(reportType)}
                className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              >
                Tạo Báo Cáo
              </button>
            </div>
          </div>
        </div>
      );
    }

    function App() {
      const [modalOpen, setModalOpen] = React.useState(false);
      const [reportType, setReportType] = React.useState(null);

      const openModal = (type) => {
        setReportType(type);
        setModalOpen(true);
      };

      const closeModal = () => {
        setModalOpen(false);
        setReportType(null);
      };

      const handleSubmit = (type) => {
        alert(`Đã tạo báo cáo ${type === 'order' ? 'đơn hàng' : ''}`);
        closeModal();
      };

      return (
        <div className="min-h-screen bg-gray-100 p-6">
          <div className="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
            <a
              href="baocaosp.php"
              className="bg-gradient-to-r from-blue-500 to-blue-700 text-white p-6 rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 cursor-pointer flex items-center space-x-4"
            >
              <i className="fas fa-box text-3xl"></i>
              <div>
                <h2 className="text-xl font-semibold">Báo Cáo Sản Phẩm</h2>
                <p className="mt-2 text-sm">Thống kê sản phẩm theo thời gian và loại.</p>
              </div>
            </a>
            <a
              href="baocaokh.php"
              className="bg-gradient-to-r from-green-500 to-green-700 text-white p-6 rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 cursor-pointer flex items-center space-x-4"
            >
              <i className="fas fa-users text-3xl"></i>
              <div>
                <h2 className="text-xl font-semibold">Báo Cáo Khách Hàng</h2>
                <p className="mt-2 text-sm">Phân tích dữ liệu khách hàng theo nhóm.</p>
              </div>
            </a>
            <a
              href="baocaodh.php"
              className="bg-gradient-to-r from-purple-500 to-purple-700 text-white p-6 rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 cursor-pointer flex items-center space-x-4"
            >
              <i className="fas fa-shopping-cart text-3xl"></i>
              <div>
                <h2 className="text-xl font-semibold">Báo Cáo Đơn Hàng</h2>
                <p className="mt-2 text-sm">Theo dõi trạng thái và số lượng đơn hàng.</p>
              </div>
            </a>
          </div>
          <ReportModal
            isOpen={modalOpen}
            onClose={closeModal}
            reportType={reportType}
            onSubmit={handleSubmit}
          />
        </div>
      );
    }

    const root = ReactDOM.createRoot(document.getElementById('root'));
    root.render(<App />);
  </script>
</body>
</html>