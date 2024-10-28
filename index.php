<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المكتبة التقنية</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }

        h1 {
            color: #333;
        }

        .form-container, .book-list, .member-list, .menu, .stats, .borrowing-details {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            max-width: 500px;
        }

        form input, form button, select {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background-color: #f0f0f0;
        }

        .hidden {
            display: none;
        }

        .message {
            color: green;
            margin: 10px 0;
        }
    </style>
</head>
<body>

    <h1>نظام إدارة المكتبة التقنية</h1>

    <!-- Menu for Choosing Action -->
    <div class="menu">
        <h2>اختر العملية</h2>
        <select id="actionMenu">
            <option value="">-- اختر --</option>
            <option value="add">إضافة كتاب</option>
            <option value="edit">تعديل كتاب</option>
            <option value="delete">حذف كتاب</option>
            <option value="borrow">استعارة كتاب</option>
        </select>
    </div>

    <!-- Form for Adding New Book -->
    <div class="form-container hidden" id="addBookForm">
        <h2>إضافة كتاب جديد</h2>
        <form method="POST">
            <input type="text" name="title" placeholder="عنوان الكتاب" required>
            <input type="text" name="language" placeholder="لغة البرمجة" required>
            <input type="text" name="author" placeholder="اسم المؤلف" required>
            <input type="number" name="price" placeholder="السعر" step="0.01" required>
            <button type="submit" name="addBook">إضافة الكتاب</button>
        </form>
    </div>

    <!-- Form for Editing a Book -->
    <div class="form-container hidden" id="editBookForm">
        <h2>تعديل كتاب</h2>
        <form method="POST">
            <input type="number" name="edit_book_id" placeholder="رقم الكتاب" required>
            <input type="text" name="new_title" placeholder="العنوان الجديد">
            <input type="text" name="new_language" placeholder="لغة البرمجة الجديدة">
            <input type="text" name="new_author" placeholder="اسم المؤلف الجديد">
            <input type="number" name="new_price" placeholder="السعر الجديد" step="0.01">
            <button type="submit" name="editBook">تعديل الكتاب</button>
        </form>
    </div>

    <!-- Form for Deleting a Book -->
    <div class="form-container hidden" id="deleteBookForm">
        <h2>حذف كتاب</h2>
        <form method="POST">
            <input type="number" name="delete_book_id" placeholder="رقم الكتاب" required>
            <button type="submit" name="deleteBook">حذف الكتاب</button>
        </form>
    </div>

    <!-- Form for Borrowing a Book -->
    <div class="form-container hidden" id="borrowBookForm">
        <h2>استعارة كتاب</h2>
        <form method="POST">
            <input type="number" name="member_id" placeholder="رقم العضو" required>
            <input type="number" name="book_id" placeholder="رقم الكتاب" required>
            <input type="date" name="borrow_date" placeholder="تاريخ الاستعارة" required>
            <input type="date" name="return_date" placeholder="تاريخ الإرجاع">
            <button type="submit" name="borrowBook">استعارة الكتاب</button>
        </form>
    </div>

    <!-- Book List -->
    <div class="book-list">
        <h2>قائمة الكتب المتاحة (الأسماء بالأحرف الكبيرة)</h2>
        <table>
            <thead>
                <tr>
                    <th>رقم الكتاب</th>
                    <th>العنوان</th>
                    <th>لغة البرمجة</th>
                    <th>المؤلف</th>
                    <th>السعر</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Connect to SQLite Database
                $db = new SQLite3('library_db.db');

                // Add Book
                if (isset($_POST['addBook'])) {
                    $title = $_POST['title'];
                    $language = $_POST['language'];
                    $author = $_POST['author'];
                    $price = $_POST['price'];

                    $stmt = $db->prepare("INSERT INTO Books (Title, Programming_Language, Author, Price, Available) VALUES (?, ?, ?, ?, 1)");
                    $stmt->bindValue(1, $title, SQLITE3_TEXT);
                    $stmt->bindValue(2, $language, SQLITE3_TEXT);
                    $stmt->bindValue(3, $author, SQLITE3_TEXT);
                    $stmt->bindValue(4, $price, SQLITE3_FLOAT);
                    $stmt->execute();

                    echo "<div class='message'>تم إضافة الكتاب بنجاح!</div>";
                }

                // Edit Book
                if (isset($_POST['editBook'])) {
                    $book_id = $_POST['edit_book_id'];
                    $new_title = $_POST['new_title'];
                    $new_language = $_POST['new_language'];
                    $new_author = $_POST['new_author'];
                    $new_price = $_POST['new_price'];

                    $update_query = "UPDATE Books SET ";
                    $params = [];
                    if (!empty($new_title)) $update_query .= "Title = ?, ";
                    if (!empty($new_language)) $update_query .= "Programming_Language = ?, ";
                    if (!empty($new_author)) $update_query .= "Author = ?, ";
                    if (!empty($new_price)) $update_query .= "Price = ?, ";

                    $update_query = rtrim($update_query, ', ') . " WHERE Book_ID = ?";
                    $stmt = $db->prepare($update_query);

                    $param_index = 1;
                    if (!empty($new_title)) $stmt->bindValue($param_index++, $new_title, SQLITE3_TEXT);
                    if (!empty($new_language)) $stmt->bindValue($param_index++, $new_language, SQLITE3_TEXT);
                    if (!empty($new_author)) $stmt->bindValue($param_index++, $new_author, SQLITE3_TEXT);
                    if (!empty($new_price)) $stmt->bindValue($param_index++, $new_price, SQLITE3_FLOAT);
                    $stmt->bindValue($param_index, $book_id, SQLITE3_INTEGER);
                    $stmt->execute();

                    echo "<div class='message'>تم تعديل الكتاب بنجاح!</div>";
                }

                // Delete Book
                if (isset($_POST['deleteBook'])) {
                    $book_id = $_POST['delete_book_id'];

                    $stmt = $db->prepare("DELETE FROM Books WHERE Book_ID = ?");
                    $stmt->bindValue(1, $book_id, SQLITE3_INTEGER);
                    $stmt->execute();

                    echo "<div class='message'>تم حذف الكتاب بنجاح!</div>";
                }

                // Borrow Book
                if (isset($_POST['borrowBook'])) {
                    $member_id = $_POST['member_id'];
                    $book_id = $_POST['book_id'];
                    $borrow_date = $_POST['borrow_date'];
                    $return_date = $_POST['return_date'];

                    // Insert into Borrowing table
                    $stmt = $db->prepare("INSERT INTO Borrowing (Member_ID, Book_ID, Borrow_Date, Return_Date) VALUES (?, ?, ?, ?)");
                    $stmt->bindValue(1, $member_id, SQLITE3_INTEGER);
                    $stmt->bindValue(2, $book_id, SQLITE3_INTEGER);
                    $stmt->bindValue(3, $borrow_date, SQLITE3_TEXT);
                    $stmt->bindValue(4, $return_date, SQLITE3_TEXT);
                    $stmt->execute();

                    echo "<div class='message'>تم استعارة الكتاب بنجاح!</div>";
                }

                // Retrieve books and display titles in uppercase
                $results = $db->query("SELECT Book_ID, UPPER(Title) AS Title, Programming_Language, Author, Price FROM Books WHERE Available = 1");
                while ($row = $results->fetchArray()) {
                    echo "<tr>
                            <td>{$row['Book_ID']}</td>
                            <td>{$row['Title']}</td>
                            <td>{$row['Programming_Language']}</td>
                            <td>{$row['Author']}</td>
                            <td>{$row['Price']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="member-list">
        <h2>قائمة الأعضاء المسجلين في المكتبة</h2>
        <table>
            <thead>
                <tr>
                    <th>رقم العضو</th>
                    <th>الاسم</th>
                    <th>تاريخ الانضمام</th>
                    <th>البريد الإلكتروني</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Connect to SQLite Database
                $db = new SQLite3('library_db.db');

                // Retrieve members
                $results = $db->query("SELECT Member_ID, Name, Join_Date, Email FROM Members");

                // Display each member in a table row
                while ($row = $results->fetchArray()) {
                    echo "<tr>
                            <td>{$row['Member_ID']}</td>
                            <td>{$row['Name']}</td>
                            <td>{$row['Join_Date']}</td>
                            <td>{$row['Email']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- Statistics for Total Books and Price -->
    <div class="stats">
        <h2>إحصائيات المكتبة</h2>
        <?php
        // Use COUNT to get the total number of available books
        $countResult = $db->querySingle("SELECT COUNT(*) AS total_books FROM Books WHERE Available = 1");
        echo "<p>إجمالي عدد الكتب المتاحة: $countResult</p>";

        // Use SUM to get the total price of all available books
        $sumResult = $db->querySingle("SELECT SUM(Price) AS total_price FROM Books WHERE Available = 1");
        echo "<p>إجمالي سعر الكتب المتاحة: $sumResult</p>";
        ?>
    </div>

    <!-- Borrowing Details using JOIN -->
    <div class="borrowing-details">
        <h2>تفاصيل الاستعارة (JOIN)</h2>
        <table>
            <thead>
                <tr>
                    <th>اسم العضو</th>
                    <th>عنوان الكتاب</th>
                    <th>تاريخ الاستعارة</th>
                    <th>تاريخ الإرجاع</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Retrieve borrowing details using JOIN
                $borrowingQuery = "
                    SELECT Members.Name AS MemberName, Books.Title AS BookTitle, 
                           Borrowing.Borrow_Date, Borrowing.Return_Date 
                    FROM Borrowing 
                    JOIN Members ON Borrowing.Member_ID = Members.Member_ID
                    JOIN Books ON Borrowing.Book_ID = Books.Book_ID
                ";
                $borrowingResults = $db->query($borrowingQuery);
                while ($row = $borrowingResults->fetchArray()) {
                    echo "<tr>
                            <td>{$row['MemberName']}</td>
                            <td>{$row['BookTitle']}</td>
                            <td>{$row['Borrow_Date']}</td>
                            <td>{$row['Return_Date']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Unborrowed Books using Subquery -->
    <div class="stats">
        <h2>الكتب التي لم يتم استعارتها (Subquery)</h2>
        <table>
            <thead>
                <tr>
                    <th>عنوان الكتاب</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Use subquery to find books that have not been borrowed
                $unborrowedBooks = "
                    SELECT Title FROM Books 
                    WHERE Book_ID NOT IN (SELECT Book_ID FROM Borrowing)
                ";
                $unborrowedResults = $db->query($unborrowedBooks);
                while ($row = $unborrowedResults->fetchArray()) {
                    echo "<tr><td>{$row['Title']}</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Books by Multiple Conditions using UNION -->
    <div class="stats">
        <h2>الكتب بناءً على شروط معينة (UNION)</h2>
        <table>
            <thead>
                <tr>
                    <th>عنوان الكتاب</th>
                    <th>لغة البرمجة</th>
                    <th>السعر</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Use UNION to find books based on multiple conditions
                $unionBooks = "
                    SELECT Title, Price, Programming_Language 
                    FROM Books 
                    WHERE Programming_Language = 'Python'   AND   Price < 1000
                    ORDER BY Price ASC;
                ";
                $unionResults = $db->query($unionBooks);
                while ($row = $unionResults->fetchArray()) {
                    echo "
                        <tr>
                            <td>{$row['Title']}</td>
                            <td>{$row['Programming_Language']}</td>
                            <td>{$row['Price']}</td>
                        </tr>
                    ";
                }

                $db->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // JavaScript for handling menu selection
        document.getElementById('actionMenu').addEventListener('change', function() {
            document.getElementById('addBookForm').classList.add('hidden');
            document.getElementById('editBookForm').classList.add('hidden');
            document.getElementById('deleteBookForm').classList.add('hidden');
            document.getElementById('borrowBookForm').classList.add('hidden');

            const selectedAction = this.value;
            if (selectedAction === 'add') {
                document.getElementById('addBookForm').classList.remove('hidden');
            } else if (selectedAction === 'edit') {
                document.getElementById('editBookForm').classList.remove('hidden');
            } else if (selectedAction === 'delete') {
                document.getElementById('deleteBookForm').classList.remove('hidden');
            } else if (selectedAction === 'borrow') {
                document.getElementById('borrowBookForm').classList.remove('hidden');
            }
        });
    </script>

</body>
</html>

