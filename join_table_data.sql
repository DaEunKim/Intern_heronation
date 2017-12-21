select Product.Name, Category.NameKR, ProductSizeList.Name, SizeType.NameKR  , ProductSize.Size 
from heronation.Product 
inner join heronation.Category on Product.CategoryID = Category.PKey
inner join heronation.ProductSizeList on ProductSizeList.ProductID = Product.PKey 
inner join heronation.ProductSize on ProductSize.ProductSizeListID = ProductSizeList.PKey 
inner join heronation.SizeType on ProductSize.SizeTypeID = SizeType.PKey 
Where Product.PKey = 60;